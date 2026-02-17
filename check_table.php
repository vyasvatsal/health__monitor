<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$result = DB::select('SHOW CREATE TABLE stores');
echo $result[0]->{'Create Table'} . "\n";
