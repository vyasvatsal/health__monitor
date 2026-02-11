<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\HealthCheck;
use App\Models\CheckResult;
use App\Models\Incident;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class TelemetryController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate Structure
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
            'checks' => 'required|array',
            'checks.*.name' => 'required|string',
            'checks.*.status' => 'required|in:ok,warning,critical',
            'checks.*.latency' => 'nullable|integer',
            'checks.*.payload' => 'nullable|array',
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

        foreach ($request->checks as $checkData) {
            // 3. Find or Create Health Check Code
            // We use name + type (defaulting to 'custom') to identify the check
            $check = HealthCheck::firstOrCreate(
                ['store_id' => $store->id, 'name' => $checkData['name']],
                [
                    'type' => $checkData['type'] ?? 'custom',
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
            if ($checkData['status'] === 'critical') {
                // Check if there is already an open incident for this check recently?
                // For MVP, we just create a new one if no open one exists
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
            'incidents_triggered' => $incidentsCreated
        ]);
    }
}
