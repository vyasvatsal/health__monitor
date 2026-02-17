<?php

/**
 * Health Monitor Laravel Handler
 * 
 * Instructions:
 * 1. Copy this file or its contents to your project's `app/Exceptions/Handler.php` (or equivalent for Laravel 11 bootstrap/app.php).
 * 2. Configure the API Key and Endpoint.
 */

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Http;
use Throwable;

class HealthMonitorHandler
{
    protected static $apiKey = 'YOUR_API_KEY_HERE';
    protected static $endpoint = 'http://127.0.0.1:8000/api/telemetry'; // Update this to your Health Monitor URL

    public static function report(Throwable $exception)
    {
        try {
            // Don't report if it's a 404 validation error? (Configurable)

            $payload = [
                'api_key' => self::$apiKey,
                'checks' => [
                    [
                        'name' => 'Server Exception: ' . get_class($exception),
                        'status' => 'critical',
                        'type' => 'server_error',
                        'latency' => 0,
                        'payload' => [
                            'message' => $exception->getMessage(),
                            'file' => $exception->getFile(),
                            'line' => $exception->getLine(),
                            'trace' => collect($exception->getTrace())->take(10)->toArray(), // Top 10 frames
                            'url' => request()->fullUrl(),
                            'method' => request()->method(),
                            'ip' => request()->ip(),
                            'user_id' => auth()->id() ?? null,
                            'timestamp' => now()->toIso8601String(),
                        ]
                    ]
                ]
            ];

            // Send asynchronously if possible, or use a queued job. 
            // For simplicity here, we use a short timeout HTTP request.
            Http::timeout(2)->post(self::$endpoint, $payload);

        } catch (\Exception $e) {
            // Fail silently to avoid infinite loops if health monitor is down
        }
    }
}
