<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapeJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'query',
        'location',
        'status',
        'total_scraped',
        'total_added',
        'total_updated',
        'error_message',
    ];

    protected $casts = [
        'total_scraped' => 'integer',
        'total_added' => 'integer',
        'total_updated' => 'integer',
    ];
}
