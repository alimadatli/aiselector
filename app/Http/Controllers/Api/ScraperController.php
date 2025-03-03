<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Models\ScrapingJob;
use App\Models\SelectorChange;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\AIAnalyzerService;
use Illuminate\Support\Facades\Log;

class ScraperController extends Controller
{
    protected $aiAnalyzer;

    public function __construct(AIAnalyzerService $aiAnalyzer)
    {
        $this->aiAnalyzer = $aiAnalyzer;
    }

    public function validate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'website_id' => 'required|exists:websites,id',
            'scraped_data' => 'required|array',
            'html_content' => 'nullable|string|min:1'
        ]);

        $website = Website::with('selectors')->findOrFail($validated['website_id']);
        
        // Create a new scraping job
        $job = ScrapingJob::create([
            'website_id' => $website->id,
            'scraped_data' => $validated['scraped_data'],
            'html_content' => $validated['html_content'],
            'status' => 'processing'
        ]);

        // Check for failed selectors
        $failedSelectors = [];
        foreach ($website->selectors as $selector) {
            if (!isset($validated['scraped_data'][$selector->name]) || 
                empty($validated['scraped_data'][$selector->name])) {
                $failedSelectors[] = [
                    'name' => $selector->name,
                    'selector' => $selector->selector,
                    'description' => $selector->description
                ];
            }
        }

        if (empty($failedSelectors)) {
            $job->update(['status' => 'success']);
            return response()->json([
                'status' => 'success',
                'message' => 'All selectors working correctly'
            ]);
        }

        if (empty($validated['html_content'])) {
            $job->update(['status' => 'failed']);
            return response()->json([
                'status' => 'error',
                'message' => 'HTML content required to analyze failed selectors',
                'failed_selectors' => $failedSelectors
            ], 422);
        }

        // Analyze and update failed selectors
        $updatedSelectors = [];
        foreach ($failedSelectors as $failed) {
            $newSelector = $this->aiAnalyzer->analyzeAndSuggestSelector(
                $validated['html_content'],
                $failed['description']
            );

            if ($newSelector) {
                $selector = $website->selectors()
                    ->where('name', $failed['name'])
                    ->first();

                // Log the selector change
                SelectorChange::create([
                    'selector_id' => $selector->id,
                    'old_selector' => $selector->selector,
                    'new_selector' => $newSelector,
                    'reason' => 'Selector failed to extract data',
                    'metadata' => [
                        'job_id' => $job->id,
                        'scraped_data' => $validated['scraped_data']
                    ]
                ]);

                $selector->update(['selector' => $newSelector]);
                $updatedSelectors[] = [
                    'name' => $failed['name'],
                    'old_selector' => $failed['selector'],
                    'new_selector' => $newSelector
                ];
            }
        }

        $job->update([
            'status' => !empty($updatedSelectors) ? 'updated' : 'failed',
            'result' => [
                'updated_selectors' => $updatedSelectors,
                'failed_selectors' => array_diff_key($failedSelectors, $updatedSelectors)
            ]
        ]);

        return response()->json([
            'status' => !empty($updatedSelectors) ? 'updated' : 'failed',
            'message' => !empty($updatedSelectors) 
                ? 'Some selectors were updated' 
                : 'Failed to update selectors',
            'updated_selectors' => $updatedSelectors,
            'failed_selectors' => array_diff_key($failedSelectors, $updatedSelectors)
        ]);
    }

    public function analyze(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'website_id' => 'required|exists:websites,id',
            'selector_name' => 'required|string',
            'html_content' => 'required|string|min:1'
        ]);

        $website = Website::findOrFail($validated['website_id']);
        $selector = $website->selectors()
            ->where('name', $validated['selector_name'])
            ->firstOrFail();

        // Use AI to analyze HTML and suggest new selector
        $newSelector = $this->aiAnalyzer->analyzeAndSuggestSelector(
            $validated['html_content'],
            $selector->description
        );

        if ($newSelector) {
            // Log the selector change
            SelectorChange::create([
                'selector_id' => $selector->id,
                'old_selector' => $selector->selector,
                'new_selector' => $newSelector,
                'reason' => 'Manual analysis request',
                'metadata' => [
                    'requested_by' => $request->user()?->id ?? 'api'
                ]
            ]);

            $selector->update(['selector' => $newSelector]);
            return response()->json([
                'status' => 'success',
                'selector_name' => $selector->name,
                'new_selector' => $newSelector,
                'message' => 'Selector updated successfully'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to generate new selector'
        ], 422);
    }
}
