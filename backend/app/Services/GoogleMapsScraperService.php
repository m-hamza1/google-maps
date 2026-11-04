<?php

namespace App\Services;

use App\Models\ScrapeJob;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class GoogleMapsScraperService
{
    public function __construct(
        protected LeadIngestionService $ingestionService
    ) {}

    /**
     * Executes the scraper for a given search query and location.
     *
     * @param string $query (e.g. 'Dentist')
     * @param string|null $location (e.g. 'Dallas, TX')
     * @param int $limit
     * @param int|null $jobId
     * @return array
     */
    public function scrape(string $query, ?string $location = null, int $limit = 20, ?int $jobId = null): array
    {
        @set_time_limit(300);

        $fullQuery = trim($query . ($location ? " in {$location}" : ''));

        $job = $jobId ? ScrapeJob::find($jobId) : ScrapeJob::create([
            'query' => $query,
            'location' => $location,
            'status' => 'processing',
        ]);

        if ($job && $job->status !== 'processing') {
            $job->update(['status' => 'processing']);
        }

        $scriptPath = base_path('scraper/scraper.js');

        if (!file_exists($scriptPath)) {
            $error = 'Scraper script not found at: ' . $scriptPath;
            if ($job) $job->update(['status' => 'failed', 'error_message' => $error]);
            return ['success' => false, 'error' => $error];
        }

        try {
            // Find node executable
            $nodeBinary = 'node';

            // Inherit full Windows environment so Node.js can initialize crypto provider (CSPRNG)
            $env = array_merge($_ENV, $_SERVER, [
                'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
                'PATH' => getenv('PATH') ?: 'C:\\Program Files\\nodejs;C:\\Windows\\system32',
                'TEMP' => getenv('TEMP') ?: sys_get_temp_dir(),
                'TMP' => getenv('TMP') ?: sys_get_temp_dir(),
            ]);

            $process = new Process([
                $nodeBinary,
                $scriptPath,
                "--query={$fullQuery}",
                "--limit={$limit}",
            ], base_path('scraper'), $env);

            $process->setTimeout(600); // 10 minutes timeout for high-volume extractions
            $process->run();

            if (!$process->isSuccessful()) {
                $errorOutput = $process->getErrorOutput() ?: $process->getOutput();
                Log::error('Scraper process failed: ' . $errorOutput);

                if ($job) {
                    $job->update([
                        'status' => 'failed',
                        'error_message' => substr($errorOutput, 0, 500),
                    ]);
                }

                return ['success' => false, 'error' => 'Scraper failed: ' . $errorOutput];
            }

            $output = trim($process->getOutput());
            $parsed = json_decode($output, true);

            if (!is_array($parsed) || empty($parsed['success'])) {
                $errMsg = $parsed['error'] ?? 'Invalid JSON output from scraper';
                if ($job) $job->update(['status' => 'failed', 'error_message' => $errMsg]);
                return ['success' => false, 'error' => $errMsg];
            }

            $rawItems = $parsed['data'] ?? [];
            
            // Ingest and cleanly upsert without overwriting user fields
            $ingestStats = $this->ingestionService->ingest($rawItems, $fullQuery, 'google_maps', $location);

            if ($job) {
                $job->update([
                    'status' => 'completed',
                    'total_scraped' => $ingestStats['total'],
                    'total_added' => $ingestStats['added'],
                    'total_updated' => $ingestStats['updated'],
                ]);
            }

            return [
                'success' => true,
                'query' => $fullQuery,
                'stats' => $ingestStats,
                'job_id' => $job?->id,
            ];
        } catch (\Exception $e) {
            Log::error('Exception in scraper execution: ' . $e->getMessage());
            if ($job) {
                $job->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
