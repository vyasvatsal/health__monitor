<?php

namespace App\Services\AI\Contracts;

interface LLMProviderInterface
{
    /**
     * Send a chat completion request.
     *
     * @param array $messages Array of ['role' => 'user|system', 'content' => '...']
     * @param array $config Optional config overrides (temperature, max_tokens, etc)
     * @return array Responds with ['content' => string, 'usage' => array]
     */
    public function chat(array $messages, array $config = []): array;

    /**
     * Analyze text and return a structured JSON response.
     * Useful for classification, root cause analysis, etc.
     *
     * @param string $systemPrompt The instruction/persona.
     * @param string $userPrompt The content to analyze.
     * @param array $schema Optional JSON schema/structure to enforce.
     * @return array
     */
    public function analyze(string $systemPrompt, string $userPrompt, array $schema = []): array;
}
