<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Store;
use App\Models\ErrorGroup;
use App\Models\ErrorEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Jobs\AnalyzeErrorJob;

// 1. Get a Test Store & Secret Key
$store = Store::whereNotNull('secret_key')->first();

if (!$store) {
    echo "No store found with secret_key. Generating keys...\n";
    // Reuse logic from generate_keys.php if needed, or just fail
    exit("Run generate_keys.php first per previous steps.\n");
}

echo "Using Store: {$store->name} (ID: {$store->id})\n";
echo "Secret Key: {$store->secret_key}\n\n";

// 2. Prepare Payload (Sentry-like structure)
$uniqueMsg = 'Verification Error V2 ' . uniqid();
$payload = [
    'exception' => [
        'type' => 'VerificationException',
        'message' => $uniqueMsg,
        'file' => '/var/www/html/app/Verification.php',
        'line' => 42,
        'trace' => "#0 /var/www/html/app/Verification.php(42): throwError()\n#1 {main}"
    ],
    'context' => [
        'user_id' => 123,
        'email' => 'test@example.com'
    ],
    'device' => [
        'platform' => 'Laravel Console',
        'version' => app()->version()
    ],
    'tags' => ['env' => 'verification']
];

// 3. Send Request to Local API
$url = 'http://127.0.0.1:8000/api/v1/track'; // Using port 8000 from .env
echo "Sending POST request to {$url}...\n";

try {
    $response = Http::withHeaders([
        'X-Monitor-Key' => $store->secret_key,
        'Accept' => 'application/json'
    ])->post($url, $payload);

    echo "Response Status: " . $response->status() . "\n";
    echo "Response Body: " . $response->body() . "\n";

    if ($response->successful()) {
        echo "SUCCESS: API accepted the payload.\n";

        // 4. Verify Database
        $group = ErrorGroup::where('store_id', $store->id)
            ->where('fingerprint', md5('VerificationException' . $payload['exception']['file'] . $payload['exception']['line'] . $payload['exception']['message']))
            // Note: Controller uses first 100 chars of message for fingerprint
            ->orWhere('title', $payload['exception']['message'])
            ->orderBy('created_at', 'desc')
            ->first();

        if ($group) {
            echo "SUCCESS: Error Group created (ID: {$group->id}).\n";

            // Check for Event
            $event = $group->events()->where('message', $uniqueMsg)->first();
            if ($event) {
                echo "SUCCESS: Error Event created (ID: {$event->id}).\n";
            } else {
                echo "FAILURE: Error Event NOT found.\n";
            }

            // Check AI Analysis Status
            // Since we didn't mock Queue, if it's sync it might be done, or if async it's queued.
            // But we can check if the column is null or not.
            // If the job ran (sync), AI analysis might still be null if the API call failed or keys are missing.
            // But at least we know the code path was hit.

            echo "AI Analysis Column: " . ($group->ai_analysis ? 'POPULATED' : 'NULL (Check Worker/Logs)') . "\n";

        } else {
            echo "FAILURE: Error Group NOT found.\n";
        }

    } else {
        echo "FAILURE: Request failed.\n";
    }

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
