<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Store;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\Http;

// 1. Get or Create a Test Store & API Key
$store = Store::first();
if (!$store) {
    echo "No store found. Creating one...\n";
    $store = Store::create([
        'name' => 'Test Store',
        'domain' => 'test.com',
        'api_key' => 'test_api_key_' . uniqid(),
        'user_id' => 1 // Assuming user 1 exists, otherwise adjustments needed
    ]);
}
$apiKey = $store->api_key;
echo "Using Store: {$store->name} (ID: {$store->id})\n";
echo "API Key: {$apiKey}\n\n";

// 2. Prepare Payload
$payload = [
    'api_key' => $apiKey,
    'checks' => [
        [
            'name' => 'Client Error: js_runtime',
            'status' => 'critical',
            'latency' => 0,
            'type' => 'client_error',
            'payload' => [
                'type' => 'js_runtime',
                'message' => 'Test Error from Verification Script ' . time(),
                'file' => 'test_script.js',
                'line' => 123
            ]
        ]
    ]
];

// 3. Send Request to Local API
$url = 'http://127.0.0.1:8000/api/v1/telemetry';
echo "Sending POST request to {$url}...\n";

try {
    $response = Http::post($url, $payload);

    echo "Response Status: " . $response->status() . "\n";
    echo "Response Body: " . $response->body() . "\n";

    if ($response->successful()) {
        echo "SUCCESS: Telemetry endpoint is working.\n";

        // 4. Verify Database
        $log = ErrorLog::where('store_id', $store->id)
            ->where('message', $payload['checks'][0]['payload']['message'])
            ->first();

        if ($log) {
            echo "SUCCESS: Error log found in database (ID: {$log->id}).\n";
        } else {
            echo "FAILURE: Error log NOT found in database.\n";
        }

    } else {
        echo "FAILURE: Request failed.\n";
    }

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
