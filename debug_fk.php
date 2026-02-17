<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Attempting to create test table with FK...\n";

try {
    Schema::dropIfExists('test_fk_issue');

    Schema::create('test_fk_issue', function (Blueprint $table) {
        $table->engine = 'InnoDB';
        $table->id();
        $table->unsignedBigInteger('store_id');
        $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
    });
    echo "Table created successfully.\n";
    Schema::drop('test_fk_issue');
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
