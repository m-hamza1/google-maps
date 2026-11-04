<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\ScrapeJob;
use App\Services\GoogleMapsScraperService;
use App\Services\LeadIngestionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected GoogleMapsScraperService $scraperService,
        protected LeadIngestionService $ingestionService
    ) {}

    public function index(Request $request)
    {
        $query = Lead::query();

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        if ($status = $request->input('status')) {
            $query->filterByStatus($status);
        }

        if ($category = $request->input('category')) {
            $query->filterByCategory($category);
        }

        if ($minRating = $request->input('min_rating')) {
            $query->filterByMinRating($minRating);
        }

        if ($city = $request->input('city')) {
            $query->filterByCity($city);
        }

        $leads = $query->latest()->paginate(20)->withQueryString();

        $totalCount = Lead::count();
        $stats = [
            'total' => $totalCount,
            'new' => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'interested' => Lead::where('status', 'interested')->count(),
            'closed' => Lead::where('status', 'closed')->count(),
            'with_phone' => Lead::whereNotNull('primary_phone')->count(),
            'with_secondary_phone' => Lead::whereNotNull('secondary_phone')->where('secondary_phone', '!=', '')->count(),
            'high_rating' => Lead::where('rating', '>=', 4.5)->count(),
        ];

        $cities = Lead::whereNotNull('city')->where('city', '!=', '')->distinct()->orderBy('city')->pluck('city')->values();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('components.dashboard.leads-table', compact('leads', 'cities', 'stats'))->render(),
                'total' => $leads->total(),
                'stats' => $stats,
            ]);
        }

        $topCategories = Lead::select('category', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('count')
            ->limit(4)
            ->get();

        $recentLeads = Lead::latest()->limit(4)->get();
        $categories = Lead::whereNotNull('category')->distinct()->pluck('category');
        $recentJobs = ScrapeJob::latest()->limit(5)->get();

        return view('dashboard', compact('leads', 'stats', 'categories', 'cities', 'recentJobs', 'topCategories', 'recentLeads'));
    }

    public function triggerScrape(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'location' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:500',
        ]);

        $query = $request->input('query');
        $location = $request->input('location');
        $limit = (int) $request->input('limit', 15);

        // Create ScrapeJob record immediately with 'pending' status
        $job = ScrapeJob::create([
            'query' => $query,
            'location' => $location,
            'status' => 'pending',
        ]);

        // Dispatch background CLI process detached from HTTP lifecycle
        $this->dispatchBackgroundScrape($job->id, $query, $location, $limit);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'job_id' => $job->id,
                'message' => "Scraper started in background for '{$query}'.",
            ]);
        }

        return back()->with('success', "Scraper started in background for '{$query}'.");
    }

    /**
     * Poll status of an asynchronous background scraping job.
     */
    public function getScrapeStatus($id)
    {
        $job = ScrapeJob::findOrFail($id);

        $message = match ($job->status) {
            'completed' => "Scraping complete! Found {$job->total_scraped}, Added {$job->total_added} new, Updated {$job->total_updated}.",
            'failed' => $job->error_message ?: 'Scraping process failed.',
            'processing' => 'Extracting leads from Google Maps in background...',
            default => 'Scraper queued in background...',
        };

        return response()->json([
            'id' => $job->id,
            'status' => $job->status,
            'query' => $job->query,
            'location' => $job->location,
            'total_scraped' => $job->total_scraped,
            'total_added' => $job->total_added,
            'total_updated' => $job->total_updated,
            'error_message' => $job->error_message,
            'message' => $message,
        ]);
    }

    /**
     * Detach and spawn artisan command as an asynchronous background OS process.
     */
    protected function dispatchBackgroundScrape(int $jobId, string $query, ?string $location, int $limit): void
    {
        $artisan = base_path('artisan');
        $php = PHP_BINARY;

        $escapedQuery = escapeshellarg($query);
        $locationOpt = $location ? '--location=' . escapeshellarg($location) : '';
        $limitOpt = "--limit={$limit}";
        $jobIdOpt = "--job-id={$jobId}";

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $cmd = "start /B \"\" \"{$php}\" \"{$artisan}\" leads:scrape {$escapedQuery} {$locationOpt} {$limitOpt} {$jobIdOpt} > NUL 2>&1";
            pclose(popen($cmd, "r"));
        } else {
            $cmd = "\"{$php}\" \"{$artisan}\" leads:scrape {$escapedQuery} {$locationOpt} {$limitOpt} {$jobIdOpt} > /dev/null 2>&1 &";
            exec($cmd);
        }
    }

    public function updateLead(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $validated = $request->validate([
            'secondary_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:new,contacted,interested,not_interested,closed',
        ]);

        $lead->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Lead '{$lead->name}' updated successfully.",
            ]);
        }

        return back()->with('success', "Lead '{$lead->name}' updated successfully.");
    }

    public function destroy($id)
    {
        $lead = Lead::findOrFail($id);
        $name = $lead->name;
        $lead->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Lead '{$name}' deleted successfully.",
            ]);
        }

        return back()->with('success', "Lead '{$name}' deleted successfully.");
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('bulk_action');
        $ids = $request->input('selected_ids', []);

        if (empty($ids) || !is_array($ids)) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No leads selected for bulk action.'], 422);
            }
            return back()->with('error', 'No leads selected for bulk action.');
        }

        if ($action === 'delete') {
            $count = Lead::whereIn('id', $ids)->delete();
            $msg = "Successfully deleted {$count} selected leads.";
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return back()->with('success', $msg);
        }

        if ($action === 'update_status') {
            $status = $request->input('new_status');
            $allowed = ['new', 'contacted', 'interested', 'not_interested', 'closed'];

            if (!in_array($status, $allowed)) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Invalid status specified for bulk update.'], 422);
                }
                return back()->with('error', 'Invalid status specified for bulk update.');
            }

            $count = Lead::whereIn('id', $ids)->update(['status' => $status]);
            $msg = "Successfully updated {$count} leads to status '{$status}'.";
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return back()->with('success', $msg);
        }

        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Unknown bulk action requested.'], 422);
        }
        return back()->with('error', 'Unknown bulk action requested.');
    }

    public function exportCsv(): StreamedResponse
    {
        $fileName = 'leads_export_' . date('Y-m-d_His') . '.csv';
        $leads = Lead::all();

        return response()->streamDownload(function () use ($leads) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID', 'Place ID', 'Name', 'Primary Phone', 'Secondary Phone',
                'Rating', 'Reviews Count', 'Category', 'Address', 'City', 'Status', 'Notes', 'Created At'
            ]);

            foreach ($leads as $lead) {
                fputcsv($handle, [
                    $lead->id,
                    $lead->place_id,
                    $lead->name,
                    $lead->primary_phone,
                    $lead->secondary_phone,
                    $lead->rating,
                    $lead->reviews_count,
                    $lead->category,
                    $lead->address,
                    $lead->city,
                    $lead->status,
                    $lead->notes,
                    $lead->created_at->toIso8601String(),
                ]);
            }
            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }
}
