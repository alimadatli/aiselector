<?php

use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\SelectorController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [WebsiteController::class, 'index'])->name('dashboard');
    Route::get('/websites/{website}/selectors', [WebsiteController::class, 'showSelectors'])->name('websites.selectors');

    // API Token Management
    Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
    Route::post('/api-tokens', [ApiTokenController::class, 'create'])->name('api-tokens.create');
    Route::delete('/api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// API routes
Route::prefix('api')->middleware(['auth'])->group(function () {
    // Website routes
    Route::apiResource('websites', WebsiteController::class);
    Route::apiResource('websites.selectors', SelectorController::class);

    // Scraper routes
    Route::post('scraper/validate', [ScraperController::class, 'validate']);
    Route::post('scraper/analyze', [ScraperController::class, 'analyze']);
});

require __DIR__.'/auth.php';
