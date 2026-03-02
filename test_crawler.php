<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Store;
use App\Services\StoreCrawlerService;

// Setup a dummy store for testing
$store = Store::first();
if (!$store) {
    echo "No store found to test.\n";
    exit;
}

// We don't want to actually crawl a massive site during this verification, 
// so we'll crawl localhost or a small test site if possible.
// For now let's just observe what happens if we point it to itself.
$store->domain = 'http://127.0.0.1:8002'; // Our local server
$store->save();

echo "Starting crawl for " . $store->domain . "...\n";

$crawler = app(\App\Services\StoreCrawlerService::class);
$crawler->crawlStore($store);

echo "Crawl complete!\n";
echo "Discovered Pages: " . \App\Models\CrawledPage::where('store_id', $store->id)->count() . "\n";
echo "Discovered CTAs: " . \App\Models\DiscoveredCta::count() . "\n";
