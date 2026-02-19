<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\LLMProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class GroqProvider implements LLMProviderInterface
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.groq.com/openai/v1';
    protected string $defaultModel;

    public function __construct(string $apiKey, string $defaultModel)
    {
        $this->apiKey = $apiKey;
        $this->defaultModel = $defaultModel;
    }

    public function chat(array $messages, array $config = []): array
    {
        return $this->sendRequest($messages, $config);
    }

    public function analyze(string $systemPrompt, string $userPrompt, array $schema = []): array
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt . "\n\nIMPORTANT: Respond ONLY with valid JSON."],
            ['role' => 'user', 'content' => $userPrompt]
        ];

        $config = [
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.1 // Low temperature for deterministic analysis
        ];

        $response = $this->sendRequest($messages, $config);

        $content = $response['content'];

        // JSON Decode Safety Check
        $decoded = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("AI JSON Decode Failed", ['content' => $content, 'error' => json_last_error_msg()]);
            // Attempt to clean markdown code blocks if present (common issue)
            $cleaned = preg_replace('/^```json\s*|\s*```$/', '', $content);
            $decoded = json_decode($cleaned, true);

            if (!$decoded) {
                throw new \Exception("AI failed to output valid JSON: " . substr($content, 0, 100));
            }
        }

        return $decoded;
    }

    protected function sendRequest(array $messages, array $config): array
    {
        $model = $config['model'] ?? $this->defaultModel;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->retry(3, 100) // Retry 3 times, 100ms sleep
                ->post($this->baseUrl . '/chat/completions', array_merge([
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 2048,
                ], $config));

            if ($response->failed()) {
                $response->throw();
            }

            $body = $response->json();

            return [
                'content' => $body['choices'][0]['message']['content'] ?? '',
                'usage' => $body['usage'] ?? [],
            ];

        } catch (RequestException $e) {
            Log::error("Groq API Error", [
                'message' => $e->getMessage(),
                'status' => $e->response?->status(),
                'body' => $e->response?->body()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error("Groq Unexpected Error", ['message' => $e->getMessage()]);
            throw $e;
        }
    }
    public function checkHealth(): array
    {
        $start = microtime(true);
        try {
            // minimal request to check connectivity
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(5)->get($this->baseUrl . '/models');

            $latency = round((microtime(true) - $start) * 1000);

            if ($response->successful()) {
                return [
                    'status' => 'ok',
                    'latency' => $latency,
                    'message' => 'Operational'
                ];
            }

            return [
                'status' => 'error',
                'latency' => $latency,
                'message' => 'API Error: ' . $response->status()
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'latency' => 0,
                'message' => 'Connection Failed: ' . $e->getMessage()
            ];
        }
    }
}
