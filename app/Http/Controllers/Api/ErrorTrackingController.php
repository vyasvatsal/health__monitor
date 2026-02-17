<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErrorEvent;
use App\Models\ErrorGroup;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ErrorTrackingController extends Controller
{
    public function capture(Request $request)
    {
        try {
            $validated = $request->validate([
                'store_id' => 'required',
                'message' => 'required|string',
                'type' => 'nullable|string',
                'file' => 'nullable|string',
                'line' => 'nullable|integer',
                'trace' => 'nullable|string',
                'url' => 'nullable|string',
                'method' => 'nullable|string',
                'ip' => 'nullable|string',
            ]);

            $store = Store::find($validated['store_id']);

            if (!$store) {
                return response()->json(['message' => 'Store not found'], 404);
            }

            // Generate a fingerprint to group similar errors
            // Group by: type + file + line + message (simplified)
            $fingerprintString = ($validated['type'] ?? 'Error') .
                ($validated['file'] ?? '') .
                ($validated['line'] ?? '') .
                $validated['message'];

            $fingerprint = md5($fingerprintString);

            // Find or Create Error Group
            $group = ErrorGroup::firstOrCreate(
                [
                    'store_id' => $store->id,
                    'fingerprint' => $fingerprint
                ],
                [
                    'title' => Str::limit($validated['message'], 250), // Title defaults to message
                    'status' => 'open',
                    'last_seen_at' => now(),
                    'count' => 0
                ]
            );

            // Update Group Stats
            $group->increment('count');
            $group->update(['last_seen_at' => now()]);

            // AI Analysis (Trigger if not exists)
            if (is_null($group->ai_analysis)) {
                // Dispatch Job asynchronously
                try {
                    \App\Jobs\AnalyzeErrorJob::dispatch($group, $validated['message'], $validated['trace'] ?? null);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to dispatch AI Job: ' . $e->getMessage());
                }
            }

            // Re-open if it was resolved
            if ($group->status === 'resolved') {
                $group->update(['status' => 'open']);
            }

            // Create Error Event
            $event = $group->events()->create([
                'message' => $validated['message'],
                'payload' => [
                    'type' => $validated['type'] ?? 'Error',
                    'file' => $validated['file'] ?? null,
                    'line' => $validated['line'] ?? null,
                    'trace' => $validated['trace'] ?? null,
                    'url' => $validated['url'] ?? null,
                    'method' => $validated['method'] ?? null,
                    'ip' => $validated['ip'] ?? null,
                    'userAgent' => $request->header('User-Agent'),
                ],
                'stack_trace' => $validated['trace'] ?? null,
                'occurred_at' => now(),
            ]);

            return response()->json([
                'message' => 'Error captured',
                'id' => $event->id,
                'group_id' => $group->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
