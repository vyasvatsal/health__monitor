<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Store;
use App\Models\ErrorGroup;
use App\Models\ErrorEvent;

$store = Store::first();

if (!$store) {
    echo "No store found. Please create a store first.\n";
    exit;
}

$group = ErrorGroup::create([
    'store_id' => $store->id,
    'fingerprint' => 'test-error-fingerprint',
    'title' => 'Test Error Group',
    'status' => 'open',
    'last_seen_at' => now(),
    'count' => 1,
]);

ErrorEvent::create([
    'error_group_id' => $group->id,
    'message' => 'This is a test error event',
    'payload' => ['foo' => 'bar'],
    'stack_trace' => 'Test stack trace...',
    'occurred_at' => now(),
]);

echo "Test error created for store: " . $store->name . "\n";
