<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\Selector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;

class ScraperController extends Controller
{
    public function validate(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'selector' => 'required|string',
            'type' => 'required|string|in:text,link,image',
        ]);

        try {
            $response = Http::get($validated['url']);
            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch URL',
                ], 400);
            }

            $html = $response->body();
            $results = $this->extractData($html, $validated['selector'], $validated['type']);

            return response()->json([
                'success' => true,
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'html' => 'required|string',
        ]);

        try {
            // Here we would integrate with DeepSeek AI or another AI service
            // to analyze the HTML and suggest selectors
            // For now, return a mock response
            return response()->json([
                'success' => true,
                'suggestions' => [
                    [
                        'selector' => '.article-title',
                        'type' => 'text',
                        'confidence' => 0.95,
                        'description' => 'Article title selector',
                    ],
                    [
                        'selector' => '.article-content',
                        'type' => 'text',
                        'confidence' => 0.90,
                        'description' => 'Article content selector',
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function extractData(string $html, string $selector, string $type): array
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html, LIBXML_NOERROR);
        $xpath = new DOMXPath($dom);

        // Convert CSS selector to XPath
        // This is a simplified version - in production you'd want to use a proper CSS to XPath converter
        $xpathSelector = "//*[contains(@class, '" . str_replace('.', '', $selector) . "')]";
        $elements = $xpath->query($xpathSelector);

        $results = [];
        foreach ($elements as $element) {
            switch ($type) {
                case 'text':
                    $results[] = trim($element->textContent);
                    break;
                case 'link':
                    if ($element->hasAttribute('href')) {
                        $results[] = $element->getAttribute('href');
                    }
                    break;
                case 'image':
                    if ($element->hasAttribute('src')) {
                        $results[] = $element->getAttribute('src');
                    }
                    break;
            }
        }

        return array_filter($results);
    }
}
