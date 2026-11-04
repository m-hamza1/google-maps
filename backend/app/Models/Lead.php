<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id',
        'name',
        'primary_phone',
        'secondary_phone',
        'rating',
        'reviews_count',
        'address',
        'city',
        'category',
        'website',
        'latitude',
        'longitude',
        'notes',
        'status',
        'search_query',
        'source',
    ];

    protected $casts = [
        'rating' => 'float',
        'reviews_count' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Scope for searching leads by name, phone, address, or notes.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('primary_phone', 'like', "%{$term}%")
              ->orWhere('secondary_phone', 'like', "%{$term}%")
              ->orWhere('address', 'like', "%{$term}%")
              ->orWhere('city', 'like', "%{$term}%")
              ->orWhere('category', 'like', "%{$term}%")
              ->orWhere('notes', 'like', "%{$term}%");
        });
    }

    /**
     * Filter by city or location.
     */
    public function scopeFilterByCity($query, ?string $city)
    {
        if (!empty($city) && $city !== 'all') {
            $city = trim($city);
            return $query->where(function ($q) use ($city) {
                $q->where('city', 'like', "%{$city}%")
                  ->orWhere('address', 'like', "%{$city}%")
                  ->orWhere('search_query', 'like', "%{$city}%");
            });
        }
        return $query;
    }

    /**
     * Filter by lead workflow status.
     */
    public function scopeFilterByStatus($query, ?string $status)
    {
        if (!empty($status) && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Filter by category.
     */
    public function scopeFilterByCategory($query, ?string $category)
    {
        if (!empty($category) && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Filter by minimum rating.
     */
    public function scopeFilterByMinRating($query, $minRating)
    {
        if (!is_null($minRating) && is_numeric($minRating)) {
            return $query->where('rating', '>=', (float) $minRating);
        }
        return $query;
    }
}
