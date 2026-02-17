<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GrokService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.x.ai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.xai.key');
    }

    /**
     * Analyze store health data and return insights using Grok.
     *
     * @param array $healthData
     * @return string
     */
    public function analyzeHealth(array $healthData)
    {
        if (!$this->apiKey) {
            return "AI Analysis Unavailable: xAI API Key not configured.";
        }

        $messages = $this->buildMessages($healthData);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->baseUrl, [
                        'model' => 'grok-2-latest',
                        'messages' => $messages,
                        'temperature' => 0.7, // Balance creativity and precision
                        'stream' => false,
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? 'No analysis generated.';
            }

            Log::error('Grok API Error: ' . $response->body());
            return "Unable to generate analysis at this time. (" . $response->status() . ")";

        } catch (\Exception $e) {
            Log::error('Grok Service Exception: ' . $e->getMessage());
            return "Error connecting to AI service.";
        }
    }

    protected function buildMessages(array $data)
    {
        $systemPrompt = "You are an elite E-commerce Performance & Revenue Optimization AI powered by Grok. 
        Your goal is to maximize the revenue and stability of the user's store.
        
        **Style Guide:**
        - Be direct, confident, and professional.
        - No fluff. Get straight to the point.
        - Use Markdown for formatting (bold, lists).";

        $userPrompt = "Analyze this store profile:
        
        **Store Profile:**
        - Name: {$data['store_name']}
        - Health Score: {$data['score']} / 100
        
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
        1. **Executive Summary**: One sentence on the current state.
        2. **3 High-Impact Fixes**: Concrete actions to take IMMEDIATELY.
        3. **Growth Strategy**: One strategic move to increase trust or UX.";

        return [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ];
    }
}
