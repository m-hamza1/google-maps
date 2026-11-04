<?php

use App\Http\Controllers\Api\LeadApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('leads')->group(function () {
    Route::get('/', [LeadApiController::class, 'index']);
    Route::get('/stats', [LeadApiController::class, 'stats']);
    Route::get('/categories', [LeadApiController::class, 'categories']);
    Route::get('/cities', [LeadApiController::class, 'cities']);
    Route::get('/jobs', [LeadApiController::class, 'jobs']);
    Route::post('/scrape', [LeadApiController::class, 'scrape']);
    Route::get('/{id}', [LeadApiController::class, 'show']);
    Route::patch('/{id}', [LeadApiController::class, 'update']);
});
