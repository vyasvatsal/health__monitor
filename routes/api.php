<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'env' => app()->environment(),
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/v1/telemetry', [\App\Http\Controllers\TelemetryController::class, 'store']);
Route::post('/v1/capture', [\App\Http\Controllers\TelemetryController::class, 'capture']);
