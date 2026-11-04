<?php

namespace App\Console\Commands;

use App\Services\GoogleMapsScraperService;
use Illuminate\Console\Command;

class ScrapeGoogleMapsCommand extends Command
{
    protected $signature = 'leads:scrape {query? : Business search keyword (e.g. "Dentists")} {--location= : Target city/area (e.g. "Dallas, TX")} {--limit=20 : Maximum listings to extract} {--job-id= : Associated ScrapeJob ID}';

    protected $description = 'Scrape targeted business leads from Google Maps without overwriting user data';

    public function handle(GoogleMapsScraperService $scraperService): int
    {
        $jobId = $this->option('job-id');
        $query = $this->argument('query');
        $location = $this->option('location');
        $limit = (int) $this->option('limit');

        if ($jobId) {
            $job = \App\Models\ScrapeJob::find($jobId);
            if ($job) {
                $query = $query ?: $job->query;
                $location = $location ?: $job->location;
            }
        }

        if (!$query) {
            $this->error('A search query is required.');
            return Command::FAILURE;
        }

        $this->info("🚀 Starting scraper for '{$query}'" . ($location ? " in '{$location}'" : '') . " (limit: {$limit}, job: " . ($jobId ?: 'none') . ")...");

        $result = $scraperService->scrape($query, $location, $limit, $jobId ? (int)$jobId : null);

        if (!$result['success']) {
            $this->error("❌ Scraping failed: " . ($result['error'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        $stats = $result['stats'];
        $this->info("✓ Scraping finished successfully!");
        $this->table(['Metric', 'Count'], [
            ['Total Scraped', $stats['total']],
            ['New Added', $stats['added']],
            ['Updated (Preserved User Data)', $stats['updated']],
        ]);

        return Command::SUCCESS;
    }
}
