<?php

use App\Models\Store;
use Illuminate\Support\Str;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$stores = Store::whereNull('public_key')->orWhereNull('secret_key')->get();

foreach ($stores as $store) {
    echo "Updating store: {$store->name}...\n";

    if (!$store->public_key) {
        $store->public_key = 'pk_' . Str::random(24);
    }

    if (!$store->secret_key) {
        $store->secret_key = 'sk_' . Str::random(24);
    }

    $store->save();
    echo "  Public Key: {$store->public_key}\n";
    echo "  Secret Key: {$store->secret_key}\n";
}

echo "Done.\n";
