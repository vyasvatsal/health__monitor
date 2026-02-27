<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Store;
use App\Models\PageMetrics;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    /**
     * Receive native frontend performance metrics (LCP, CLS, Load Time)
     * Auth is handled via a private Tracking Key in the headers, so public users can't spam it easily
     */
    public function track(Request $request)
    {
        $trackingKey = $request->header('X-Private-Tracking-Key');

        if (!$trackingKey) {
            return response()->json(['error' => 'Missing Private Tracking Key'], 401);
        }

        $store = Store::where('private_tracking_key', $trackingKey)->first();

        if (!$store) {
            return response()->json(['error' => 'Invalid Tracking Key'], 401);
        }

        $validated = $request->validate([
            'url_path' => 'required|string|max:255',
            'device_type' => 'nullable|string|in:desktop,mobile',
            'load_time_ms' => 'nullable|integer',
            'js_time_ms' => 'nullable|integer',
            'vitals' => 'nullable|array',
            'cta_clicks' => 'nullable|array',
        ]);

        // Clean up the URL path (remove scheme/host if they sent the full URL)
        $urlPath = $validated['url_path'];
        $parsed = parse_url($urlPath);
        $cleanPath = ($parsed['path'] ?? '/') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');

        try {
            // Later we can implement a background Job to Grade this asynchronously to save API response time
            // For now, save synchronously
            $metric = PageMetrics::create([
                'store_id' => $store->id,
                'url_path' => $cleanPath,
                'device_type' => $validated['device_type'] ?? 'desktop',
                'load_time_ms' => $validated['load_time_ms'],
                'js_time_ms' => $validated['js_time_ms'],
                'vitals' => $validated['vitals'],
                'cta_clicks' => $validated['cta_clicks'],
                // Grader logic will run asynchronously later
                'grade' => null
            ]);

            return response()->json(['success' => true, 'id' => $metric->id], 201);
        } catch (\Exception $e) {
            Log::error('TrackingController Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process tracking data'], 500);
        }
    }
}
