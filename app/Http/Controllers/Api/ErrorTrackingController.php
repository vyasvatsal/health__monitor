<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\ErrorGroup;
use App\Models\ErrorEvent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ErrorTrackingController extends Controller
{
    /**
     * Ingest error reports from SDKs
     */
    public function store(Request $request)
    {
        // 1. Authenticate via Header
        $monitorKey = $request->header('X-Monitor-Key');

        if (!$monitorKey) {
            return response()->json(['error' => 'Missing X-Monitor-Key header'], 401);
        }

        // Try finding by Public Key (Frontend) or Secret Key (Backend)
        $store = Store::where('public_key', $monitorKey)
            ->orWhere('secret_key', $monitorKey)
            ->first();

        if (!$store) {
            // Fallback for backward compatibility (legacy api_key)
            $store = Store::where('api_key', $monitorKey)->first();
        }

        if (!$store) {
            return response()->json(['error' => 'Invalid Monitor Key'], 401);
        }

        // 2. Validate Payload
        $validator = Validator::make($request->all(), [
            'exception.message' => 'required|string',
            'exception.type' => 'nullable|string',
            'exception.file' => 'nullable|string',
            'exception.line' => 'nullable|integer',
            'exception.trace' => 'nullable', // string or array
            'context' => 'nullable|array',
            'device' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid payload', 'details' => $validator->errors()], 422);
        }

        $data = $request->all();
        $exception = $data['exception'];

        // 3. Fingerprinting (Group Errors)
        // Create a unique hash based on message + file + line + type
        // Use a simplified message for grouping to avoid duplicates from variable content
        $fingerprintSource = $exception['type'] .
            $exception['file'] .
            $exception['line'] .
            substr($exception['message'], 0, 100);

        $fingerprint = md5($fingerprintSource);

        $errorGroup = ErrorGroup::firstOrCreate(
            [
                'store_id' => $store->id,
                'fingerprint' => $fingerprint
            ],
            [
                'title' => substr($exception['message'], 0, 250),
                'status' => 'open',
                'last_seen_at' => now(),
                'count' => 0
            ]
        );

        // 4. Update Stats
        $errorGroup->increment('count');
        $errorGroup->update(['last_seen_at' => now()]);

        // 5. Store Event
        $trace = is_array($exception['trace']) ? json_encode($exception['trace']) : $exception['trace'];

        $event = ErrorEvent::create([
            'error_group_id' => $errorGroup->id,
            'message' => $exception['message'],
            'stack_trace' => $trace,
            'payload' => [
                'type' => $exception['type'] ?? 'Error',
                'file' => $exception['file'] ?? 'unknown',
                'line' => $exception['line'] ?? 0,
                'context' => $data['context'] ?? [],
                'device' => $data['device'] ?? [],
                'tags' => $data['tags'] ?? [],
                'ip' => $request->ip(),
                'userAgent' => $request->header('User-Agent'),
            ],
            'occurred_at' => now(),
        ]);

        // 6. Trigger AI Analysis - REMOVED

        return response()->json([
            'status' => 'success',
            'event_id' => $event->id
        ], 201);
    }
}
