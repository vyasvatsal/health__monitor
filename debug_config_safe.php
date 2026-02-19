<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- ENV CHECK ---\n";
$envKey = env('GROQ_API_KEY');
echo "GROQ_API_KEY from env(): " . ($envKey ? substr($envKey, 0, 5) . '...' : 'NULL') . "\n";

echo "\n--- CONFIG CHECK ---\n";
$configKey = config('ai.drivers.groq.api_key');
echo "config('ai.drivers.groq.api_key'): " . ($configKey ? substr($configKey, 0, 5) . '...' : 'NULL') . "\n";

echo "config('ai'): " . print_r(config('ai'), true) . "\n";

echo "\n--- FILE CHECK ---\n";
echo "config/ai.php exists: " . (file_exists(config_path('ai.php')) ? 'YES' : 'NO') . "\n";
