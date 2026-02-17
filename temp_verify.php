<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;
use App\Models\ErrorGroup;

try {
    echo "Sending request...\n";
    $response = Http::post('http://127.0.0.1:8000/api/v1/capture', [
        'store_id' => 1,
        'message' => 'Tesing AI Analysis ' . time(),
        'type' => 'Error',
        'file' => '/app/test.php',
        'line' => 123,
        'trace' => 'Stack trace...'
    ]);

    echo "Response status: " . $response->status() . "\n";

    // Check DB
    $group = ErrorGroup::latest('updated_at')->first();
    if ($group) {
        echo "Latest Group ID: " . $group->id . "\n";
        if ($group->ai_analysis) {
            echo "AI Analysis found: Yes\n";
            print_r($group->ai_analysis);
        } else {
            echo "AI Analysis found: No (Might be null or failed)\n";
        }
    } else {
        echo "No error group found.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
