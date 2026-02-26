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
}
