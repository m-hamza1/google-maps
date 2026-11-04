<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadIngestionService
{
    /**
     * Ingests a collection of scraped or imported leads safely.
     * Preserves user modifications (secondary_phone, notes, status).
     *
     * @param array $rawLeads
     * @param string|null $searchQuery
     * @param string $source
     * @return array ['total' => int, 'added' => int, 'updated' => int]
     */
    public function ingest(array $rawLeads, ?string $searchQuery = null, string $source = 'google_maps', ?string $location = null): array
    {
        $added = 0;
        $updated = 0;
        $total = count($rawLeads);
        $batchProcessedSignatures = [];

        foreach ($rawLeads as $raw) {
            $cleaned = $this->sanitizeLead($raw, $searchQuery, $source, $location);
            if (!$cleaned) {
                continue;
            }

            // In-batch deduplication: prevent identical duplicates within the same scrape run
            $batchSig = strtolower($cleaned['name']) . '|' . strtolower($cleaned['city'] ?? '') . '|' . ($cleaned['primary_phone'] ?? '');
            if (isset($batchProcessedSignatures[$batchSig])) {
                continue;
            }
            $batchProcessedSignatures[$batchSig] = true;

            try {
                // Multi-factor Deduplication Strategy:
                // 1. Match by Google Place ID
                $existing = Lead::where('place_id', $cleaned['place_id'])->first();

                // 2. Match by primary phone (if valid phone exists)
                if (!$existing && !empty($cleaned['primary_phone'])) {
                    $existing = Lead::where('primary_phone', $cleaned['primary_phone'])->first();
                }

                // 3. Match by normalized Name + City
                if (!$existing && !empty($cleaned['name']) && !empty($cleaned['city'])) {
                    $existing = Lead::whereRaw('LOWER(name) = ?', [strtolower($cleaned['name'])])
                        ->whereRaw('LOWER(city) = ?', [strtolower($cleaned['city'])])
                        ->first();
                }

                // 4. Match by normalized Name + Address prefix (first 25 characters)
                if (!$existing && !empty($cleaned['name']) && !empty($cleaned['address'])) {
                    $addrPrefix = substr(strtolower(trim($cleaned['address'])), 0, 25);
                    if (strlen($addrPrefix) >= 10) {
                        $existing = Lead::whereRaw('LOWER(name) = ?', [strtolower($cleaned['name'])])
                            ->whereRaw('LOWER(address) LIKE ?', [$addrPrefix . '%'])
                            ->first();
                    }
                }

                if ($existing) {
                    // Update only scraped system fields — preserve user edits!
                    $existing->update([
                        'name' => $cleaned['name'],
                        'primary_phone' => $cleaned['primary_phone'] ?? $existing->primary_phone,
                        'rating' => $cleaned['rating'] ?? $existing->rating,
                        'reviews_count' => $cleaned['reviews_count'] ?? $existing->reviews_count,
                        'address' => $cleaned['address'] ?? $existing->address,
                        'city' => $cleaned['city'] ?? $existing->city,
                        'category' => $cleaned['category'] ?? $existing->category,
                        'website' => $cleaned['website'] ?? $existing->website,
                        'latitude' => $cleaned['latitude'] ?? $existing->latitude,
                        'longitude' => $cleaned['longitude'] ?? $existing->longitude,
                        'search_query' => $cleaned['search_query'] ?? $existing->search_query,
                    ]);
                    $updated++;
                } else {
                    // Create brand-new record
                    Lead::create($cleaned);
                    $added++;
                }
            } catch (\Exception $e) {
                Log::warning("Failed to ingest lead: " . $e->getMessage(), ['raw' => $raw]);
            }
        }

        return [
            'total' => $total,
            'added' => $added,
            'updated' => $updated,
        ];
    }

    /**
     * Clean, sanitize, and normalize lead data.
     */
    public function sanitizeLead(array $item, ?string $searchQuery = null, string $source = 'google_maps', ?string $location = null): ?array
    {
        $name = trim(strip_tags((string) ($item['name'] ?? $item['title'] ?? '')));
        $name = preg_replace('/[\x{E000}-\x{F8FF}]/u', '', $name);
        $name = trim(preg_replace('/\s+/', ' ', $name));
        if (empty($name)) {
            return null;
        }

        $address = trim(strip_tags((string) ($item['address'] ?? '')));
        $address = preg_replace('/[\x{E000}-\x{F8FF}]/u', '', $address);
        $address = trim(preg_replace('/\s+/', ' ', $address));

        $category = trim(strip_tags((string) ($item['category'] ?? '')));
        $category = preg_replace('/[\x{E000}-\x{F8FF}]/u', '', $category);
        $category = trim(preg_replace('/\s+/', ' ', $category));
        $website = trim(strip_tags((string) ($item['website'] ?? '')));
        
        // Detect / Extract City
        $city = !empty($location) ? trim($location) : null;
        if (empty($city) && !empty($item['city'])) {
            $city = trim($item['city']);
        }
        if (empty($city) && !empty($searchQuery) && preg_match('/\s+in\s+([^,\-]+)/i', $searchQuery, $matches)) {
            $city = trim($matches[1]);
        }
        if (empty($city) && !empty($address)) {
            $knownCities = [
                'Lahore', 'Karachi', 'Islamabad', 'Rawalpindi', 'Faisalabad', 'Multan',
                'Peshawar', 'Quetta', 'Sialkot', 'Gujranwala', 'Hyderabad', 'Gujrat',
                'Dallas', 'Houston', 'Austin', 'New York', 'Los Angeles', 'Chicago',
                'London', 'Dubai', 'Toronto', 'Sydney'
            ];
            foreach ($knownCities as $kc) {
                if (stripos($address, $kc) !== false || stripos($name, $kc) !== false) {
                    $city = $kc;
                    break;
                }
            }
        }
        
        // Sanitize Phone Number
        $rawPhone = (string) ($item['phone'] ?? $item['primary_phone'] ?? '');
        $primaryPhone = $this->formatPhoneNumber($rawPhone);

        // Sanitize Rating & Reviews
        $rating = null;
        if (isset($item['rating']) && is_numeric($item['rating'])) {
            $r = (float) $item['rating'];
            if ($r > 0 && $r <= 5) {
                $rating = round($r, 1);
            }
        }

        $reviewsCount = 0;
        if (isset($item['reviews_count']) && is_numeric($item['reviews_count'])) {
            $reviewsCount = max(0, (int) $item['reviews_count']);
        } elseif (isset($item['reviews']) && is_numeric($item['reviews'])) {
            $reviewsCount = max(0, (int) $item['reviews']);
        }

        // Sanitize Coordinates
        $latitude = isset($item['latitude']) && is_numeric($item['latitude']) ? (float) $item['latitude'] : null;
        $longitude = isset($item['longitude']) && is_numeric($item['longitude']) ? (float) $item['longitude'] : null;

        // Generate or normalize Place ID
        $placeId = trim((string) ($item['place_id'] ?? ''));
        if (empty($placeId)) {
            // Deterministic hash based on name + address
            $placeId = 'local_' . hash('sha256', strtolower($name) . '|' . strtolower($address));
        }

        return [
            'place_id' => $placeId,
            'name' => $name,
            'primary_phone' => $primaryPhone,
            'secondary_phone' => null, // User can set this in Flutter app
            'rating' => $rating,
            'reviews_count' => $reviewsCount,
            'address' => !empty($address) ? $address : null,
            'city' => !empty($city) ? ucwords(strtolower($city)) : null,
            'category' => !empty($category) ? $category : null,
            'website' => !empty($website) ? $website : null,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'notes' => null,
            'status' => 'new',
            'search_query' => $searchQuery,
            'source' => $source,
        ];
    }

    /**
     * Clean and normalize phone numbers.
     */
    protected function formatPhoneNumber(string $phone): ?string
    {
        $phone = trim($phone);
        if (empty($phone)) {
            return null;
        }

        // Remove unwanted text characters, preserving +, (, ), -, and digits
        $cleaned = preg_replace('/[^\d\+\(\)\-\s\.]/', '', $phone);
        $cleaned = preg_replace('/\s+/', ' ', trim($cleaned));

        // Must have at least 6 digits to be considered a valid phone
        $digits = preg_replace('/\D/', '', $cleaned);
        if (strlen($digits) < 6) {
            return null;
        }

        return $cleaned;
    }
}
