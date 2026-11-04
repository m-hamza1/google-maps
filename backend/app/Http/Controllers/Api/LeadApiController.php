<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\ScrapeJob;
use App\Services\GoogleMapsScraperService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadApiController extends Controller
{
    public function __construct(
        protected GoogleMapsScraperService $scraperService
    ) {}

    /**
     * Get paginated leads with full search and filtering capabilities.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Lead::query();

        // Search in Name, Phone, Address, Notes, Category
        if ($search = $request->input('search', $request->input('q'))) {
            $query->search($search);
        }

        // Filter by Status
        if ($status = $request->input('status')) {
            $query->filterByStatus($status);
        }

        // Filter by Category
        if ($category = $request->input('category')) {
            $query->filterByCategory($category);
        }

        // Filter by City
        if ($city = $request->input('city')) {
            $query->filterByCity($city);
        }

        // Filter by Minimum Rating
        if ($minRating = $request->input('min_rating')) {
            $query->filterByMinRating($minRating);
        }

        // Filter: only with phone
        if ($request->boolean('has_phone')) {
            $query->whereNotNull('primary_phone');
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        $allowedSorts = ['created_at', 'rating', 'reviews_count', 'name', 'status'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, strtolower($sortOrder) === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $perPage = min(100, max(5, (int) $request->input('per_page', 15)));
        $leads = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'pagination' => [
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
                'has_more' => $leads->hasMorePages(),
            ],
        ]);
    }

    /**
     * Get single lead details.
     */
    public function show($id): JsonResponse
    {
        $lead = Lead::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $lead,
        ]);
    }

    /**
     * Update user-editable lead fields:
     * Secondary Phone Number, Notes, and Status.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $lead = Lead::findOrFail($id);

        $validated = $request->validate([
            'secondary_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:new,contacted,interested,not_interested,closed',
        ]);

        $lead->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully.',
            'data' => $lead,
        ]);
    }

    /**
     * Get aggregated metrics and stats for dashboard & mobile overview.
     */
    public function stats(): JsonResponse
    {
        $totalLeads = Lead::count();
        $statusCounts = Lead::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $withPhone = Lead::whereNotNull('primary_phone')->count();
        $withSecondaryPhone = Lead::whereNotNull('secondary_phone')->count();
        $withNotes = Lead::whereNotNull('notes')->where('notes', '!=', '')->count();

        $topCategories = Lead::select('category', DB::raw('count(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('count')
            ->limit(8)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_leads' => $totalLeads,
                'with_primary_phone' => $withPhone,
                'with_secondary_phone' => $withSecondaryPhone,
                'with_notes' => $withNotes,
                'statuses' => [
                    'new' => $statusCounts['new'] ?? 0,
                    'contacted' => $statusCounts['contacted'] ?? 0,
                    'interested' => $statusCounts['interested'] ?? 0,
                    'not_interested' => $statusCounts['not_interested'] ?? 0,
                    'closed' => $statusCounts['closed'] ?? 0,
                ],
                'top_categories' => $topCategories,
            ],
        ]);
    }

    /**
     * Get distinct categories for filters.
     */
    public function categories(): JsonResponse
    {
        $categories = Lead::whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get distinct cities for filters.
     */
    public function cities(): JsonResponse
    {
        $cities = Lead::whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }

    /**
     * Admin/Trigger Scraping on-demand.
     */
    public function scrape(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:200',
            'location' => 'nullable|string|max:150',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $query = $request->input('query');
        $location = $request->input('location');
        $limit = (int) $request->input('limit', 20);

        $result = $this->scraperService->scrape($query, $location, $limit);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Scraping failed',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Scraping completed successfully.',
            'data' => $result,
        ]);
    }

    /**
     * Get history of scraping jobs.
     */
    public function jobs(): JsonResponse
    {
        $jobs = ScrapeJob::latest()->limit(20)->get();

        return response()->json([
            'success' => true,
            'data' => $jobs,
        ]);
    }
}
