<?php

use Illuminate\Support\Facades\Http;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$storeId = \App\Models\Store::first()->id ?? 1;
$url = 'http://127.0.0.1:8000/api/v1/capture';

echo "Sending test error to {$url} for Store ID: {$storeId}...\n";

$response = Http::post($url, [
    'store_id' => $storeId,
    'type' => 'SimulatedError',
    'message' => 'This is a test error to verify the monitoring UI.',
    'file' => 'c:/xampp/htdocs/health_monitor/test_script.php',
    'line' => 123,
    'trace' => "Stack trace:\n#0 {main}\n#1 /vendor/framework/src/Illuminate/Pipeline/Pipeline.php(10): Illuminate\Support\Facades\Http::post()\n...",
    'url' => 'http://127.0.0.1:8000/test',
    'method' => 'GET',
    'ip' => '127.0.0.1'
]);

if ($response->successful()) {
    echo "Success! Error ID: " . $response->json()['id'] . "\n";
} else {
    echo "Failed! Status: " . $response->status() . "\n";
    echo $response->body() . "\n";
}
