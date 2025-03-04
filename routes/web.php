<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebsiteController;
use App\Http\Controllers\Api\SelectorController;
use App\Http\Controllers\Api\ScraperController;
use App\Http\Controllers\Api\ApiTokenController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// API Routes
Route::prefix('api')->group(function () {
    // Website routes
    Route::apiResource('websites', WebsiteController::class);

    // Selector routes (nested under websites)
    Route::get('websites/{website}/selectors', [SelectorController::class, 'index']);
    Route::post('websites/{website}/selectors', [SelectorController::class, 'store']);
    Route::get('websites/{website}/selectors/{selector}', [SelectorController::class, 'show']);
    Route::put('websites/{website}/selectors/{selector}', [SelectorController::class, 'update']);
    Route::delete('websites/{website}/selectors/{selector}', [SelectorController::class, 'destroy']);

    // Scraper routes
    Route::post('scraper/validate', [ScraperController::class, 'validate']);
    Route::post('scraper/analyze', [ScraperController::class, 'analyze']);

    // API Token Management
    Route::middleware(['auth'])->group(function () {
        Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
        Route::post('/api-tokens', [ApiTokenController::class, 'create'])->name('api-tokens.create');
        Route::delete('/api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
    });
});
