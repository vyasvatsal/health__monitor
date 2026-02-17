<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// \Illuminate\Support\Facades\Schedule::command('monitor:daily-digest')->dailyAt('08:00');
// \Illuminate\Support\Facades\Schedule::command('monitor:run-benchmarks')->hourly();
