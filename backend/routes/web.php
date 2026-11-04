<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard & Lead Management
Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
Route::post('/scrape', [AdminDashboardController::class, 'triggerScrape'])->name('scrape.trigger');
Route::get('/scrape/status/{id}', [AdminDashboardController::class, 'getScrapeStatus'])->name('scrape.status');
Route::patch('/leads/{id}', [AdminDashboardController::class, 'updateLead'])->name('leads.update');
Route::delete('/leads/{id}', [AdminDashboardController::class, 'destroy'])->name('leads.destroy');
Route::post('/leads/bulk-action', [AdminDashboardController::class, 'bulkAction'])->name('leads.bulkAction');
Route::get('/export/csv', [AdminDashboardController::class, 'exportCsv'])->name('leads.export');

