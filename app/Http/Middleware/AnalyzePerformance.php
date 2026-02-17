<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AnalyzePerformance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = (microtime(true) - $start) * 1000; // ms

        // Threshold: 300ms
        if ($duration > 300) {
            $url = $request->fullUrl();
            $method = $request->method();

            // Log to file
            Log::warning("Slow Query Detected: [{$method}] {$url} - {$duration}ms");

            // Cache for Dashboard Display (Latest Slowest)
            // We store the slowest one found in the last hour
            $currentSlowest = Cache::get('performance_slowest_route', ['duration' => 0]);

            if ($duration > $currentSlowest['duration']) {
                Cache::put('performance_slowest_route', [
                    'url' => $url,
                    'method' => $method,
                    'duration' => round($duration, 2),
                    'timestamp' => now()->toDateTimeString()
                ], 3600); // 1 hour TTL
            }
        }

        return $response;
    }
}
