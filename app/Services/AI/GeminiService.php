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

        **Analysis Request:**
        1. **Executive Summary**: One sentence on the current state (e.g., 'Leaking revenue due to latency' or 'Prime for growth').
        2. **3 High-Impact Fixes**: Concrete actions to take IMMEDIATELY. If performance is low, suggest specific Laravel optimizations (e.g., 'Cache routes', 'Optimize queries').
        3. **Growth Strategy**: One strategic move to increase trust or UX based on the current scores.

        *Format the output as a clean Markdown list. Be direct, professional, and confident.*";
    }
}
