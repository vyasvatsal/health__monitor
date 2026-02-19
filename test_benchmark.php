<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Store;
use App\Models\Competitor;
use App\Services\Benchmarking\BenchmarkRunner;

echo "--- Testing Benchmark Runner ---\n";

// 1. Setup Data
$store = Store::first();
if (!$store) {
    die("No store found.\n");
}
// Ensure store has a domain
if (empty($store->domain)) {
    $store->update(['domain' => 'https://example.com']);
}

// Create/Get Competitor
$competitor = Competitor::firstOrCreate(
    ['store_id' => $store->id, 'url' => 'https://google.com'],
    ['name' => 'Google Test']
);

// 2. Run Benchmark
echo "Running benchmark against {$competitor->url} vs {$store->domain}...\n";
$runner = new BenchmarkRunner();
$result = $runner->run($store, $competitor);

// 3. Inspect Result
echo "Winner: " . $result->winner . "\n";
echo "My TTFB: " . $result->my_ttfb_ms . "ms\n";
echo "Comp TTFB: " . $result->competitor_ttfb_ms . "ms\n";

// Check Details structure
$details = $result->details;
echo "\nChecking Details Structure:\n";
print_r($details['my_assets']['seo'] ?? 'Missing SEO');
echo "\n";
print_r($details['my_assets']['ally'] ?? 'Missing Ally');

if (isset($details['my_assets']['seo']) && isset($details['my_assets']['ally'])) {
    echo "\nSUCCESS: New metrics found in result.\n";
} else {
    echo "\nFAILURE: New metrics missing.\n";
}
