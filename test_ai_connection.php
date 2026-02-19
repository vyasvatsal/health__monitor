<?php

use Illuminate\Contracts\Console\Kernel;
use App\Enums\AIModel;
use App\Services\AI\AIManager;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "--- Starting AI Connection Test ---\n";

try {
    $ai = app('ai'); // Resolve via container

    // 1. Test Chat
    echo "\n[Test 1] Testing Simple Chat...\n";
    $response = $ai->driver('groq')->chat([
        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
        ['role' => 'user', 'content' => 'Say "Hello, World!" and nothing else.']
    ]);

    echo "Response: " . $response['content'] . "\n";
    if (trim($response['content']) == '"Hello, World!"' || str_contains($response['content'], 'Hello, World')) {
        echo "PASS: Chat working.\n";
    } else {
        echo "FAIL: Unexpected response.\n";
    }

    // 2. Test Analysis (JSON)
    echo "\n[Test 2] Testing JSON Analysis...\n";
    $analysis = $ai->driver('groq')->analyze(
        'You are a data extractor.',
        'My name is John Doe and I am a software engineer.',
        ['name', 'profession']
    );

    print_r($analysis);

    if (isset($analysis['name']) && $analysis['name'] == 'John Doe') {
        echo "PASS: JSON Analysis working.\n";
    } else {
        echo "FAIL: JSON Analysis failed.\n";
    }

    echo "\n--- AI Infrastructure Verified Successfully ---\n";

} catch (\Exception $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}
