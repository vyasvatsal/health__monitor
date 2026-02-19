<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n========== AI DIAGNOSIS ==========\n";

// 1. Env Check
$envKey = env('GROQ_API_KEY');
echo "[1] .env Key Check: " . ($envKey ? "FOUND (" . substr($envKey, 0, 8) . "...)" : "MISSING") . "\n";

// 2. Config Check
$configKey = config('ai.drivers.groq.api_key');
echo "[2] Config Key Check: " . ($configKey ? "FOUND" : "MISSING") . "\n";

if (!$envKey) {
    echo "CRITICAL: GROQ_API_KEY is missing from .env. Please add it.\n";
    echo "Example: GROQ_API_KEY=gsk_...\n";
    exit(1);
}

// 3. Raw HTTP Check (Curl)
echo "[3] Testing Raw Connection to Groq API...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.groq.com/openai/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $envKey",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "model" => "llama-3.3-70b-versatile",
    "messages" => [
        ["role" => "user", "content" => "Say 'Connection Successful'"]
    ]
]));

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "    HTTP Status: $httpCode\n";
if ($httpCode === 200) {
    $json = json_decode($result, true);
    echo "    Response: " . ($json['choices'][0]['message']['content'] ?? "INVALID FORMAT") . "\n";
} else {
    echo "    ERROR: " . $result . "\n";
}

// 4. Service Check
echo "[4] Testing Laravel AI Service...\n";
try {
    $response = app('ai')->chat([
        ['role' => 'user', 'content' => 'Say Service Working']
    ]);
    echo "    Service Response: " . $response['content'] . "\n";
} catch (\Exception $e) {
    echo "    Service Error: " . $e->getMessage() . "\n";
}

echo "==================================\n";
