<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebsiteController;
use App\Http\Controllers\Api\SelectorController;
use App\Http\Controllers\Api\ScraperController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Website routes
Route::apiResource('websites', WebsiteController::class);

// Selector routes (nested under websites)
Route::get('websites/{website}/selectors', [SelectorController::class, 'index']);
Route::post('websites/{website}/selectors', [SelectorController::class, 'store']);
Route::get('websites/{website}/selectors/{selector}', [SelectorController::class, 'show']);
Route::put('websites/{website}/selectors/{selector}', [SelectorController::class, 'update']);
Route::delete('websites/{website}/selectors/{selector}', [SelectorController::class, 'destroy']);
Route::get('websites/{website}/selectors/{selector}/changes', [SelectorController::class, 'changes']);

// Scraper routes
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('scraper/validate', [ScraperController::class, 'validate']);
    Route::post('scraper/analyze', [ScraperController::class, 'analyze']);
});
