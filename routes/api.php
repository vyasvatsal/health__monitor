<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelemetryController;

Route::middleware('throttle:60,1')->prefix('v1')->group(function () {
    Route::post('/telemetry', [TelemetryController::class, 'store']);
    Route::post('/ai/analyze', [App\Http\Controllers\Api\AIController::class, 'analyze'])
        ->middleware('subscribed:pro');
});
