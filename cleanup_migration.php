<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Disable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Drop tables if they exist
Schema::dropIfExists('error_events');
Schema::dropIfExists('error_groups');
Schema::dropIfExists('error_tracking_tables');

// Remove the migration entry
DB::table('migrations')->where('migration', 'like', '%create_error_tracking_tables%')->delete();

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Database cleanup completed.\n";
