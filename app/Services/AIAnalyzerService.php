<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIAnalyzerService
{
    protected $apiKey;
    protected $apiEndpoint;
    protected $maxRetries = 3;

    public function __construct()
    {
        $this->apiKey = config('services.deepseek.api_key');
        $this->apiEndpoint = config('services.deepseek.endpoint');
    }

    public function analyzeAndSuggestSelector(string $html, string $description): ?string
    {
        try {
            // Clean and parse HTML first
            $cleanHtml = $this->cleanHtml($html);
            if (empty($cleanHtml)) {
                Log::error('Invalid HTML content provided');
                return null;
            }

            // Try to find a selector with retries
            for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
                $selector = $this->requestAIAnalysis($cleanHtml, $description);
                if ($selector && $this->validateSelector($selector, $cleanHtml)) {
                    return $selector;
                }
                Log::warning("Attempt $attempt failed to find valid selector");
            }

            Log::error('Failed to find valid selector after all attempts');
            return null;

        } catch (\Exception $e) {
            Log::error('Error analyzing HTML', [
                'error' => $e->getMessage(),
                'description' => $description
            ]);
            return null;
        }
    }

    protected function requestAIAnalysis(string $html, string $description): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiEndpoint, [
            'model' => 'deepseek-coder-33b',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are an expert in HTML and CSS selectors. Your task is to analyze HTML structure and suggest precise, reliable CSS selectors that will continue to work even if the page structure changes slightly. Focus on unique identifiers and stable attributes. Avoid using indices or overly specific selectors that might break easily."
                ],
                [
                    'role' => 'user',
                    'content' => "Given this HTML content:\n\n{$html}\n\nI need a CSS selector to extract: {$description}\n\nProvide only the selector, nothing else. The selector should be specific enough to uniquely identify the element but robust enough to survive minor HTML changes."
                ]
            ],
            'temperature' => 0.3,
            'max_tokens' => 100
        ]);

        if ($response->successful()) {
            $result = $response->json();
            return $this->extractSelectorFromResponse($result['choices'][0]['message']['content']);
        }

        Log::error('AI API request failed', [
            'status' => $response->status(),
            'response' => $response->json()
        ]);
        return null;
    }

    protected function cleanHtml(string $html): string
    {
        // Remove comments
        $html = preg_replace('/<!--[\s\S]*?-->/', '', $html);
        
        // Remove scripts and style tags
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        
        // Remove extra whitespace
        $html = preg_replace('/\s+/', ' ', $html);
        
        return trim($html);
    }

    protected function validateSelector(string $selector, string $html): bool
    {
        try {
            // Use DOMDocument to test the selector
            $dom = new \DOMDocument();
            @$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            
            $xpath = new \DOMXPath($dom);
            
            // Convert CSS selector to XPath
            $xpathSelector = $this->cssToXPath($selector);
            
            // Test if selector finds exactly one element
            $elements = $xpath->query($xpathSelector);
            
            return $elements && $elements->length === 1;
        } catch (\Exception $e) {
            Log::error('Error validating selector', [
                'selector' => $selector,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    protected function cssToXPath(string $selector): string
    {
        // Basic CSS to XPath conversion
        // This is a simplified version - you might want to use a proper CSS to XPath converter
        $selector = trim($selector);
        
        if (strpos($selector, '#') === 0) {
            // ID selector
            return "//*[@id='" . substr($selector, 1) . "']";
        }
        
        if (strpos($selector, '.') === 0) {
            // Class selector
            return "//*[contains(@class,'" . substr($selector, 1) . "')]";
        }
        
        // Direct element selector
        return "//{$selector}";
    }

    protected function extractSelectorFromResponse(string $response): ?string
    {
        // Remove any markdown formatting
        $response = trim(preg_replace('/`/', '', $response));
        
        // Validate the selector format
        if (empty($response) || strlen($response) > 255) {
            return null;
        }
        
        // Basic validation of selector characters
        if (!preg_match('/^[a-zA-Z0-9\-_\[\]()>+~. #:="\'*^$|,]+$/', $response)) {
            return null;
        }
        
        return $response;
    }
}