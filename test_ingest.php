<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$store = \App\Models\Store::first();
if (!$store) {
    echo "No store found.\n";
    exit;
}

$payload = [
    'events' => [
        [
            'type' => 'health',
            'timestamp' => now()->toISOString(),
            'memory_usage_mb' => 25.5,
            'cpu_load' => 1.2,
            'db_connected' => true,
        ]
    ]
];

$request = \Illuminate\Http\Request::create('/api/ingest', 'POST', [], [], [], [
    'HTTP_X-Monitor-Key' => $store->public_key ?? $store->api_key,
    'HTTP_X-Project-Id' => $store->id,
    'CONTENT_TYPE' => 'application/json',
], json_encode($payload));

$response = app()->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";

echo "CheckResult count: " . \App\Models\CheckResult::count() . "\n";
echo "HealthCheck count: " . \App\Models\HealthCheck::count() . "\n";
