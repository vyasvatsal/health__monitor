<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\ErrorGroup;
use App\Models\ErrorEvent;
use App\Jobs\AnalyzeErrorJob;
use Illuminate\Support\Str;

class IngestController extends Controller
{
    /**
     * Ingest batch payloads from the SDK
     * Endpoint: /api/ingest
     */
    public function store(Request $request)
    {
        // 1. Authenticate via Header (extracted from DSN in the SDK Transport)
        $monitorKey = $request->header('X-Monitor-Key');
        $projectId = $request->header('X-Project-Id');

        if (!$monitorKey) {
            return response()->json(['error' => 'Missing Project Key in DSN'], 401);
        }

        $query = Store::query()
            ->where(function ($q) use ($monitorKey) {
                $q->where('public_key', $monitorKey)
                    ->orWhere('secret_key', $monitorKey)
                    ->orWhere('api_key', $monitorKey);
            });

        if ($projectId) {
            $query->where('id', $projectId);
        }

        $store = $query->first();

        if (!$store) {
            return response()->json(['error' => 'Invalid Monitor Key or Project ID'], 401);
        }

        // Sync Project Metadata
        $store->update(['last_seen_at' => now()]);

        if ($request->has('app_name') && !empty($request->app_name)) {
            // Only update name if it's the default "Project #ID" or if explicitly requested
            if (str_starts_with($store->name, 'Project #') || $store->name === 'New Project') {
                $store->update(['name' => $request->app_name]);
            }
        }

        // 2. Validate Events Array
        $events = $request->input('events');

        // If empty, try to explicitly decode the request content (handles cases where Content-Type is missing)
        if (empty($events)) {
            $payload = json_decode($request->getContent(), true);
            $events = $payload['events'] ?? null;
        }

        if (!is_array($events) || empty($events)) {
            return response()->json(['error' => 'No events provided'], 422);
        }

        $processedEvents = [];

        // 3. Process each event in the batch
        foreach ($events as $eventData) {
            // Determine the type: exception or log or transaction or health
            $type = $eventData['type'] ?? 'log';

            if ($type === 'health') {
                $healthCheck = \App\Models\HealthCheck::firstOrCreate(
                    [
                        'store_id' => $store->id,
                        'name' => 'System Health',
                        'type' => 'system'
                    ]
                );

                \App\Models\CheckResult::create([
                    'health_check_id' => $healthCheck->id,
                    'status' => ($eventData['db_connected'] ?? false) ? 'ok' : 'critical',
                    'payload' => [
                        'memory_usage_mb' => isset($eventData['memory_usage_mb']) ? (int) ceil((float) $eventData['memory_usage_mb']) : null,
                        'cpu_load' => $eventData['cpu_load'] ?? null,
                        'db_connected' => $eventData['db_connected'] ?? false,
                    ],
                    'created_at' => \Carbon\Carbon::parse($eventData['timestamp'] ?? now()),
                ]);

                $processedEvents[] = 'health_' . $healthCheck->id;
                continue;
            }

            if ($type === 'transaction') {
                $txn = \App\Models\PerformanceTransaction::create([
                    'store_id' => $store->id,
                    'route_name' => $eventData['route_name'] ?? 'unknown',
                    'method' => $eventData['method'] ?? 'GET',
                    'url' => $eventData['url'] ?? '',
                    'duration_ms' => isset($eventData['duration_ms']) ? (int) $eventData['duration_ms'] : 0,
                    'memory_usage_mb' => isset($eventData['memory_usage_mb']) ? (int) ceil((float) $eventData['memory_usage_mb']) : 0,
                    'env' => $eventData['env'] ?? 'production',
                    'payload' => ['status_code' => $eventData['status_code'] ?? 200],
                    'occurred_at' => \Carbon\Carbon::parse($eventData['timestamp'] ?? now()),
                ]);

                $processedEvents[] = 'txn_' . $txn->id;
                continue;
            }

            $message = $eventData['message'] ?? 'Unknown Error';

            // fingerprinting
            if ($type === 'exception') {
                $fingerprintSource = ($eventData['exception'] ?? '') .
                    ($eventData['file'] ?? '') .
                    ($eventData['line'] ?? '') .
                    substr($message, 0, 100);
            } else {
                $fingerprintSource = ($eventData['level'] ?? 'info') .
                    substr($message, 0, 100);
            }

            $fingerprint = md5($fingerprintSource);

            // Grouping
            $errorGroup = ErrorGroup::firstOrCreate(
                [
                    'store_id' => $store->id,
                    'fingerprint' => $fingerprint
                ],
                [
                    'title' => substr($message, 0, 250),
                    'status' => 'open',
                    'last_seen_at' => now(),
                    'count' => 0
                ]
            );

            $errorGroup->increment('count');
            $errorGroup->update(['last_seen_at' => now()]);

            // Save the individual event
            $eventRecord = ErrorEvent::create([
                'error_group_id' => $errorGroup->id,
                'message' => $message,
                'stack_trace' => $type === 'exception' ? ($eventData['trace'] ?? null) : null,
                'payload' => [
                    'type' => $type,
                    'exception_class' => $eventData['exception'] ?? null,
                    'file' => $eventData['file'] ?? null,
                    'line' => $eventData['line'] ?? null,
                    'level' => $eventData['level'] ?? null,
                    'context' => $eventData['context'] ?? [],
                    'url' => $eventData['url'] ?? null,
                    'method' => $eventData['method'] ?? null,
                    'env' => $eventData['env'] ?? 'production',
                    'ip' => $request->ip(),
                    'userAgent' => $request->header('User-Agent'),
                ],
                'occurred_at' => \Carbon\Carbon::parse($eventData['timestamp'] ?? now()),
            ]);

            $processedEvents[] = $eventRecord->id;

            // Trigger AI Analysis for new errors
            if ($errorGroup->count == 1 || is_null($errorGroup->ai_solution)) {
                AnalyzeErrorJob::dispatch($errorGroup)->afterCommit();
            }
        }

        return response()->json([
            'status' => 'success',
            'processed' => count($processedEvents),
            'event_ids' => $processedEvents
        ], 201);
    }
}
