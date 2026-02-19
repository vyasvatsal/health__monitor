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

        return new GroqProvider(
            $config['api_key'],
            $config['model'] ?? AIModel::default()->value
        );
    }
}
