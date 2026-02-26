<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.google.api_key');
    }

    /**
     * Analyze store health data and return insights.
     *
     * @param array $healthData
     * @return string
     */
    public function analyzeHealth(array $healthData)
    {
        if (!$this->apiKey) {
            return "AI Analysis Unavailable: Google API Key not configured.";
        }

        $prompt = $this->buildHealthAnalysisPrompt($healthData);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}?key={$this->apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt]
                                ]
                            ]
                        ]
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No analysis generated.';
            }

            Log::error('Gemini API Error: ' . $response->body());
            return "Unable to generate analysis at this time.";

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return "Error connecting to AI service.";
        }
    }

    protected function buildHealthAnalysisPrompt(array $data)
    {
        return "You are an elite E-commerce Performance & Revenue Optimization AI. Your goal is to maximize the revenue and stability of the following store.

        **Store Profile:**
        - Name: {$data['store_name']}
        - Health Score: {$data['score']} / 100
        - Architecture: Laravel / PHP
        
        **Critical Metrics:**
        - Performance: {$data['performance_score']} (Target: >90)
        - UX Score: {$data['ux_score']} (Target: >85)
        - Trust Score: {$data['trust_score']} (Target: >95)
        - SEO Score: {$data['seo_score']} (Target: >90)
        
        **Detected Issues:**
        " . (empty($data['issues']) ? "No critical technical issues detected." : implode(", ", $data['issues'])) . "

        **Recent System Alerts (Context):**
        " . (empty($data['recent_alerts']) ? "No recent alerts." : implode(", ", $data['recent_alerts'])) . "

        **Analysis Request:**
        1. **Executive Summary**: One sentence on the current state (e.g., 'Leaking revenue due to latency' or 'Prime for growth').
        2. **3 High-Impact Fixes**: Concrete actions to take IMMEDIATELY. If performance is low, suggest specific Laravel optimizations (e.g., 'Cache routes', 'Optimize queries').
        3. **Growth Strategy**: One strategic move to increase trust or UX based on the current scores.

        *Format the output as a clean Markdown list. Be direct, professional, and confident.*";
    }

    /**
     * Analyze deep scan (Lighthouse) data and return UX/CTA insights.
     */
    public function analyzeDeepScan(array $scanData)
    {
        if (!$this->apiKey) {
            return "AI Analysis Unavailable: Google API Key not configured.";
        }

        $prompt = $this->buildDeepScanPrompt($scanData);

        try {
            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}?key={$this->apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt]
                                ]
                            ]
                        ]
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No analysis generated.';
            }

            Log::error('Gemini API Error (Deep Scan): ' . $response->body());
            return "Unable to generate deep scan analysis at this time.";
        } catch (\Exception $e) {
            Log::error('Gemini Service Exception (Deep Scan): ' . $e->getMessage());
            return "Error connecting to AI service for deep scan.";
        }
    }

    /**
     * Analyze a batch of deep scan (Lighthouse) data and return aggregated UX/CTA insights.
     */
    public function analyzeBatch(array $batchData)
    {
        if (!$this->apiKey) {
            return "AI Analysis Unavailable: Google API Key not configured.";
        }

        $prompt = $this->buildBatchPrompt($batchData);

        try {
            $response = Http::timeout(120)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}?key={$this->apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt]
                                ]
                            ]
                        ]
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No analysis generated.';
            }

            Log::error('Gemini API Error (Batch Scan): ' . $response->body());
            return "Unable to generate batch scan analysis at this time.";
        } catch (\Exception $e) {
            Log::error('Gemini Service Exception (Batch Scan): ' . $e->getMessage());
            return "Error connecting to AI service for batch scan.";
        }
    }

    protected function buildDeepScanPrompt(array $data)
    {
        // Convert the AI feed (opportunities/issues from Lighthouse) to a readable string
        $issuesList = "None recorded.";
        if (!empty($data['ai_feed'])) {
            $issuesList = collect($data['ai_feed'])->map(function ($issue) {
                return "- " . ($issue['title'] ?? 'Unknown Issue') .
                    (isset($issue['savings_ms']) ? " (Saves ~{$issue['savings_ms']}ms)" : "") .
                    (isset($issue['description']) ? ": {$issue['description']}" : "");
            })->implode("\n");
        }

        $scores = $data['scores'] ?? [];
        $vitals = $data['web_vitals'] ?? [];

        return "You are an elite UX/UI and Conversion Rate Optimization (CRO) AI expert. Analyze the following Google Lighthouse deep-scan data for a webpage and provide actionable, high-impact advice to improve Call-To-Action (CTA) placements, User Experience (UX), and technical structure.

        **Core Web Vitals & Scores:**
        - Performance: " . ($scores['performance'] ?? 'N/A') . " / 100
        - Accessibility: " . ($scores['accessibility'] ?? 'N/A') . " / 100
        - Best Practices: " . ($scores['best_practices'] ?? 'N/A') . " / 100
        - SEO: " . ($scores['seo'] ?? 'N/A') . " / 100
        - Largest Contentful Paint (LCP): " . ($vitals['lcp']['displayValue'] ?? 'N/A') . "
        - Total Blocking Time (TBT): " . ($vitals['tbt']['displayValue'] ?? 'N/A') . "
        - Cumulative Layout Shift (CLS): " . ($vitals['cls']['displayValue'] ?? 'N/A') . "

        **Detected Structural & Performance Opportunities (Lighthouse Feed):**
        {$issuesList}

        **Analysis Request:**
        1. **UX & Conversion Verdict**: Start with a potent 1-2 sentence verdict on how these metrics currently affect the user's journey and likelihood to convert (bounce rate risk, etc).
        2. **CTA & Layout Strategy**: Based on the Performance and Accessibility data (and any layout shifts), suggest *specifically* how to position or style primary CTAs so they aren't missed or accidentally clicked during page load. 
        3. **Top 3 Technical Fixes**: List the 3 most urgent technical fixes from the opportunities list above, explaining *why* they matter to the user in plain English (not just developer jargon). 

        *Important: Format the output in clean, modern Markdown. Do not use generic fluff. Be highly specific, authoritative, and focused on revenue/conversion optimization.*";
    }

    protected function buildBatchPrompt(array $batchData)
    {
        $promptContext = "You are an elite eCommerce UX and Conversion Rate Optimization AI expert. You are analyzing a BATCH report of multiple pages across a single storefront. Your goal is to provide a master CTA and Layout Strategy for the entire site based on these aggregated vitals.\n\n";
        
        $promptContext .= "### Scanned Pages Data:\n";
        foreach ($batchData as $page) {
            $promptContext .= "- **URL:** {$page['url']}\n";
            $promptContext .= "  - Performance: {$page['performance_score']}/100 | Accessibility: {$page['accessibility_score']}/100\n";
            $promptContext .= "  - LCP: " . ($page['core_web_vitals']['lcp']['displayValue'] ?? 'N/A') . " | CLS: " . ($page['core_web_vitals']['cls']['displayValue'] ?? 'N/A') . "\n";
            
            if (!empty($page['ai_feed'])) {
                $topIssue = collect($page['ai_feed'])->first();
                $promptContext .= "  - Top Issue: " . ($topIssue['title'] ?? 'N/A') . " (Saves ~" . ($topIssue['savings_ms'] ?? 0) . "ms)\n";
            }
            $promptContext .= "\n";
        }

        $promptContext .= "### Request:\n";
        $promptContext .= "1. **Sitewide Verdict**: A 2-sentence summary of the site's overall conversion health based on the speed and stability of the pages scanned.\n";
        $promptContext .= "2. **Global CTA Strategy**: Where should they place and how should they design their primary Buttons/CTAs across the funnel (Home -> Shop -> Contact) to maximize conversions given their current speed (LCP) and visual shift (CLS) traits? Be very specific.\n";
        $promptContext .= "3. **Worst Performing Area**: Identify the page or specific technical issue that is causing the biggest bottleneck in the funnel and explain how fixing it will lift revenue.\n\n";
        
        $promptContext .= "*Important: Format output in clean Markdown. Be highly actionable.*";

        return $promptContext;
    }
}
