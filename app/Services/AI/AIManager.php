<?php

namespace App\Services\AI;

use App\Enums\AIModel;
use App\Services\AI\Providers\GroqProvider;
use Illuminate\Support\Manager;

class AIManager extends Manager
{
    public function getDefaultDriver()
    {
        return $this->config->get('ai.default', 'groq');
    }

    public function createGroqDriver()
    {
        $config = $this->config->get('ai.drivers.groq');

        $apiKey = $config['api_key'] ?? null;

        if (empty($apiKey)) {
            throw new \RuntimeException('Groq API Key is not configured. Please set GROQ_API_KEY in your .env file or environment variables.');
        }

        return new GroqProvider(
            $apiKey,
            $config['model'] ?? AIModel::default()->value
        );
    }
}
