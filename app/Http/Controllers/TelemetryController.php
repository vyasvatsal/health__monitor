<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\HealthCheck;
use App\Models\CheckResult;
use App\Models\Incident;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\ErrorLog;

class TelemetryController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate Structure
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
            'checks' => 'required|array',
            'checks.*.name' => 'required|string',
            'checks.*.status' => 'required|string', // Removed strict 'in' validation to allow flexibility, or keep if types are known
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

                // Fingerprint the error to group duplicates
                // For JS: message + file + line
                // For PHP: message + file + line
                $message = $payload['message'] ?? 'Unknown Error';
                $file = $payload['file'] ?? null;
                $line = $payload['line'] ?? null;

                $existingError = ErrorLog::where('store_id', $store->id)
                    ->where('message', $message)
                    ->where('file', $file)
                    ->where('line', $line)
                    ->where('status', '!=', 'resolved')
                    ->first();

                if ($existingError) {
                    $existingError->increment('count');
                    $existingError->update(['last_seen_at' => now()]);
                } else {
                    ErrorLog::create([
                        'store_id' => $store->id,
                        'type' => $payload['type'] ?? ($type === 'client_error' ? 'JS Runtime' : 'Server Error'),
                        'message' => $message,
                        'file' => $file,
                        'line' => $line,
                        'trace' => $payload['trace'] ?? null,
                        'context' => $payload['context'] ?? $payload, // Fallback to full payload
                        'severity' => 'critical', // Defaulting to critical for reported errors
                        'status' => 'new',
                        'last_seen_at' => now(),
                    ]);
                }
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
}
