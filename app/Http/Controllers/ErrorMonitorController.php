<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\ErrorGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ErrorMonitorController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Store Selection Logic (Same as Dashboard)
        $allStores = Store::where('user_id', $user->id)->get();
        $storeId = $request->query('store_id');

        if ($storeId) {
            $store = $allStores->where('id', $storeId)->first();
        } else {
            $store = $allStores->first();
        }

        if (!$store) {
            return redirect()->route('stores.create'); // Or show empty state
        }

        // 2. Query Error Groups for this Store
        $query = ErrorGroup::where('store_id', $store->id)
            ->withCount('events'); // Efficient count

        // Filter by Status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('fingerprint', 'like', "%{$search}%");
            });
        }

        // 3. Pagination
        // Fetch error groups with their latest event to get file/line/trace details if needed for the list preview
        // Note: ErrorGroup has 'title' which we used as the message. 
        // We can load the latest event to get 'file', 'line', etc.
        $errorGroups = $query->with([
            'events' => function ($q) {
                $q->orderBy('occurred_at', 'desc')->limit(1);
            }
        ])
            ->orderBy('last_seen_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // 4. Statistics
        $stats = [
            'total_24h' => ErrorGroup::where('store_id', $store->id)
                ->where('last_seen_at', '>=', now()->subDay())->count(), // Approximation: groups seen in last 24h

            'critical' => ErrorGroup::where('store_id', $store->id)
                ->where('status', 'open') // Assuming 'open' roughly maps to critical/unresolved for now
                ->count(),

            'resolved' => ErrorGroup::where('store_id', $store->id)
                ->where('status', 'resolved')
                ->count(),

            // Impacted Users - Count distinct IPs from all events for this store
            'impacted_users' => ErrorGroup::where('store_id', $store->id)
                ->join('error_events', 'error_groups.id', '=', 'error_events.error_group_id')
                ->distinct('error_events.payload->ip')
                ->count('error_events.payload->ip')
        ];

        // 5. Transform Data for View/AlpineJS
        // We'll pass the paginated collection directly, but we map it to a format the frontend expects
        $formattedErrors = $errorGroups->getCollection()->map(function ($group) {
            $latestEvent = $group->events->first();
            $payload = is_string($latestEvent?->payload) ? json_decode($latestEvent?->payload, true) : ($latestEvent?->payload ?? []);

            // Determine severity/type from the group title or event payload
            $type = $payload['type'] ?? 'Error';

            // Attempt to resolve real types for logs out of the payload
            $level = null;
            if ($type === 'log') {
                $level = $payload['context']['level'] ?? 'info';
            }

            // Assign severity
            $severity = 'info';
            if (in_array($type, ['javascript_error', 'promise_rejection', 'Error', 'Exception'])) {
                $severity = 'critical';
            } elseif (in_array($type, ['network_error', 'resource_error', 'Warning']) || $level === 'warning') {
                $severity = 'warning';
            } elseif ($level === 'error' || $level === 'critical' || $level === 'emergency') {
                $severity = 'critical';
            }

            $aiSolution = is_string($group->ai_analysis) ? json_decode($group->ai_analysis, true) : ($group->ai_analysis ?? null);

            return [
                'id' => $group->id,
                'type' => $type,
                'level' => $level,
                'message' => $aiSolution['title'] ?? $group->title,
                'raw_message' => $group->title,
                'file' => $payload['file'] ?? 'unknown',
                'line' => $payload['line'] ?? 0,
                'trace' => $latestEvent?->stack_trace ?? '',
                'timestamp' => $group->last_seen_at->diffForHumans(),
                'status' => $group->status,
                'severity' => $severity,
                'users_impacted' => $group->count > 0 ? $group->count : 1, // Simplified metric
                'browser' => $payload['userAgent'] ?? 'Unknown',
                'occurrences' => $group->count,
                'ai_analysis' => $aiSolution ? [
                    'severity_score' => $severity === 'critical' ? 9 : ($severity === 'warning' ? 6 : 3), // Mock score
                    'title' => $aiSolution['title'] ?? 'Error',
                    'summary' => $aiSolution['explanation'] ?? '',
                    'root_cause' => $aiSolution['explanation'] ?? '',
                    'code_fix' => $aiSolution['fix'] ?? '',
                    'solution_steps' => [$aiSolution['fix'] ?? 'Please review the error trace.'],
                    'prevention' => 'Monitor this code path for similar issues.',
                ] : null
            ];
        });

        // Replace the collection in the paginator with the formatted one (optional, or just pass array)
        // But paginator expects the original items. Let's pass 'rows' separately.

        return view('monitor.errors', [
            'allStores' => $allStores,
            'currentStore' => $store,
            'stats' => $stats,
            'errors' => $formattedErrors, // Formatted array for Alpine
            'paginator' => $errorGroups // For links()
        ]);
    }
}
