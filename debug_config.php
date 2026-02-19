<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "GROQ_API_KEY from env(): " . var_export(env('GROQ_API_KEY'), true) . "\n";
echo "config('ai.drivers.groq'): " . var_export(config('ai.drivers.groq'), true) . "\n";
echo "AIManager Config: " . var_export(app('ai')->driver('groq'), true) . "\n";
