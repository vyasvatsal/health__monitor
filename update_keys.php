<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Store;
use Illuminate\Support\Str;

$stores = Store::whereNull('public_key')->orWhereNull('secret_key')->get();

foreach ($stores as $store) {
    if (!$store->public_key) {
        $store->public_key = 'pk_' . Str::random(24);
    }
    if (!$store->secret_key) {
        $store->secret_key = 'sk_' . Str::random(32);
    }
    $store->save();
    echo "Updated store ID: {$store->id}\n";
}

echo "Done.\n";
