<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAnalysisService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.groq.com/openai/v1';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY_CHATBOT');
    }

    public function analyzeError($message, $trace = null, $context = [])
    {
        if (!$this->apiKey) {
            return null;
        }

        try {
            $prompt = $this->buildPrompt($message, $trace);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                        'model' => 'llama-3.3-70b-versatile', // Using a robust model on Groq
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'You are a Senior Software Architect and expert debugger. value accuracy and clarity. Analyze the error and provide a comprehensive JSON response with the following keys:
- "title": A short, human-readable headline for the error (e.g., "Database Connection Timeout" or "Syntax Error in Search Logic").
- "summary": A 1-sentence explanation for a non-technical manager.
- "root_cause": A technical explanation of exactly what went wrong. If the error message is raw code, explain that it\'s likely a source code dump and identify the potential issue within it.
- "solution_steps": An array of strings with step-by-step instructions to fix it.
- "code_fix": A markdown code snippet showing the fix (if applicable, otherwise null).
- "prevention": A tip to prevent this from happening again.
- "severity_score": An integer from 1 (minor) to 10 (critical).
- "is_raw_code": Boolean, true if the input error message appears to be raw source code rather than a standard error message.'
                            ],
                            [
                                'role' => 'user',
                                'content' => $prompt
                            ]
                        ],
                        'response_format' => ['type' => 'json_object'],
                        'temperature' => 0.1
                    ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'];
                return json_decode($content, true);
            }

            Log::error('AI Analysis Failed: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('AI Analysis Error: ' . $e->getMessage());
            return null;
        }
    }

    protected function buildPrompt($message, $trace)
    {
        $traceSnippet = $trace ? substr($trace, 0, 1000) : 'No trace available';
        return "Error Message: {$message}\n\nStack Trace Snippet:\n{$traceSnippet}";
    }
}
