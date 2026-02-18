<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\HealthCheck;
use App\Models\CheckResult;
use App\Models\Incident;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\ErrorGroup;
use App\Models\ErrorEvent;

class TelemetryController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate Structure
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
            'checks' => 'required|array',
            'checks.*.name' => 'required|string',
            'checks.*.status' => 'required|string',
            'checks.*.latency' => 'nullable|integer',
            'checks.*.payload' => 'nullable|array',
            'checks.*.type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid payload', 'details' => $validator->errors()], 422);
        }

        // 2. Authenticate Store
        $store = Store::where('api_key', $request->api_key)->first();
        if (!$store) {
            return response()->json(['error' => 'Invalid API Key'], 401);
        }

        $ingested = 0;
        $incidentsCreated = 0;
        $errorsLogged = 0;

        foreach ($request->checks as $checkData) {
            $type = $checkData['type'] ?? 'custom';

            // Handle Error Logs (Client/Server Errors)
            if (in_array($type, ['client_error', 'server_error'])) {
                $payload = $checkData['payload'] ?? [];

                $message = $payload['message'] ?? 'Unknown Error';
                $file = $payload['file'] ?? $payload['filename'] ?? 'unknown'; // Handle JS 'filename'
                $line = $payload['line'] ?? $payload['lineno'] ?? 0; // Handle JS 'lineno'
                $trace = $payload['trace'] ?? null;

                // 1. Generate Fingerprint
                // Combine message, file, and line to create a unique signature
                $fingerprintString = $message . $file . $line;
                $fingerprint = md5($fingerprintString);

                // 2. Find or Create Error Group
                $errorGroup = ErrorGroup::firstOrCreate(
                    [
                        'store_id' => $store->id,
                        'fingerprint' => $fingerprint
                    ],
                    [
                        'title' => substr($message, 0, 250), // Limit title length
                        'status' => 'open',
                        'last_seen_at' => now(),
                        'count' => 0 // Will be incremented
                    ]
                );

                // 3. Update Group Stats
                $errorGroup->increment('count');
                $errorGroup->update(['last_seen_at' => now()]);

                // 4. Create Error Event
                ErrorEvent::create([
                    'error_group_id' => $errorGroup->id,
                    'message' => $message,
                    'payload' => array_merge($payload, [
                        'file' => $file,
                        'line' => $line,
                        'userAgent' => $request->header('User-Agent'),
                        'ip' => $request->ip()
                    ]),
                    'stack_trace' => is_array($trace) ? json_encode($trace) : $trace,
                    'occurred_at' => now(),
                ]);

                $errorsLogged++;
            }

            // Always record as Health Check Result too (optional, but good for timeline)
            // 3. Find or Create Health Check Code
            $check = HealthCheck::firstOrCreate(
                ['store_id' => $store->id, 'name' => $checkData['name']],
                [
                    'type' => $type,
                    'config' => [],
                    'is_active' => true
                ]
            );

            // 4. Record Result
            CheckResult::create([
                'health_check_id' => $check->id,
                'status' => $checkData['status'],
                'latency_ms' => $checkData['latency'] ?? 0,
                'payload' => $checkData['payload'] ?? [],
                'created_at' => now(),
            ]);

            // 5. Incident Logic (Simple Rule: Critical = Incident)
            // Only create incidents for non-error types OR if you want incidents for errors too?
            // Let's keep Incidents for "Checks" separately for now, or maybe errors create incidents?
            if ($checkData['status'] === 'critical' && !in_array($type, ['client_error', 'server_error'])) {
                $existing = Incident::where('store_id', $store->id)
                    ->where('title', 'like', "%{$check->name}%")
                    ->where('status', 'open')
                    ->first();

                if (!$existing) {
                    $incident = Incident::create([
                        'store_id' => $store->id,
                        'title' => "Critical Failure: {$check->name}",
                        'description' => "Reported status: critical. Latency: " . ($checkData['latency'] ?? 'N/A') . "ms",
                        'severity' => 'critical',
                        'status' => 'open'
                    ]);
                    $incidentsCreated++;

                    // Notify Owner
                    $store->user->notify(new \App\Notifications\CriticalIncidentOccurred($incident));
                }
            }

            $ingested++;
        }

        return response()->json([
            'status' => 'success',
            'ingested' => $ingested,
            'incidents_triggered' => $incidentsCreated,
            'errors_logged' => $errorsLogged
        ]);
    }
    /**
     * Simplified endpoint for direct error capturing from SDKs.
     */
    public function capture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required', // Can be integer or string
            'message' => 'required|string',
            'type' => 'nullable|string',
            'file' => 'nullable|string',
            'line' => 'nullable|integer',
            'trace' => 'nullable|string',
            'url' => 'nullable|string',
            'method' => 'nullable|string',
            'ip' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Invalid data'], 422);
        }

        // Authenticate Store by ID (Simpler for this endpoint as per user request)
        $store = Store::find($request->store_id);
        if (!$store) {
            return response()->json(['status' => 'error', 'message' => 'Store not found'], 404);
        }

        $message = substr($request->message, 0, 1000);
        $file = $request->file ?? 'unknown';
        $line = $request->line ?? 0;
        $trace = $request->trace;

        // 1. Fingerprint
        $fingerprint = md5($message . $file . $line);

        // 2. Find or Create Group
        $errorGroup = ErrorGroup::firstOrCreate(
            ['store_id' => $store->id, 'fingerprint' => $fingerprint],
            [
                'title' => substr($message, 0, 250),
                'status' => 'open',
                'last_seen_at' => now(),
                'count' => 0
            ]
        );

        // 3. Update Group
        $errorGroup->increment('count');
        $errorGroup->update(['last_seen_at' => now()]);

        // 4. Create Event
        ErrorEvent::create([
            'error_group_id' => $errorGroup->id,
            'message' => $message,
            'payload' => [
                'type' => $request->type ?? 'Unknown',
                'file' => $file,
                'line' => $line,
                'url' => $request->url,
                'method' => $request->method,
                'ip' => $request->ip ?? $request->ip(),
                'userAgent' => $request->header('User-Agent'),
            ],
            'stack_trace' => $trace,
            'occurred_at' => now(),
        ]);

        return response()->json(['status' => 'success']);
    }
}
