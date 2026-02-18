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

            // Determine severity/type from the group title or event payload
            // For now, simple logic:
            $type = 'ERROR';
            if (stripos($group->title, 'Warning') !== false)
                $type = 'WARNING';
            if (stripos($group->title, 'Exception') !== false)
                $type = 'EXCEPTION';

            // Calculate Impacted Users (Approximation based on IP for now)
            $impactedUsers = $group->events()->distinct('payload->ip')->count('payload->ip');

            // Determine Severity
            $severity = 'info';
            $aiScore = $group->ai_analysis['severity_score'] ?? 0;

            if ($aiScore >= 7 || $group->count > 50) {
                $severity = 'critical';
            } elseif ($aiScore >= 4 || $group->count > 10) {
                $severity = 'warning';
            }

            return [
                'id' => $group->id,
                'type' => $type,
                'message' => $group->ai_analysis['title'] ?? $group->title, // Use AI Title if available
                'raw_message' => $group->title, // Keep raw message for details
                'file' => $latestEvent->payload['file'] ?? 'unknown',
                'line' => $latestEvent->payload['line'] ?? 0,
                'trace' => $latestEvent->stack_trace ?? '',
                'timestamp' => $group->last_seen_at->diffForHumans(),
                'status' => $group->status,
                'severity' => $severity,
                'users_impacted' => $impactedUsers > 0 ? $impactedUsers : 1, // Default to at least 1 if we can't track
                'browser' => $latestEvent->payload['userAgent'] ?? 'Unknown',
                'occurrences' => $group->count,
                'ai_analysis' => $group->ai_analysis
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
