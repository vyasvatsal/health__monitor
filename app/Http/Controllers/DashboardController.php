<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HealthCheck;
use App\Models\CheckResult;
use App\Models\Incident;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user)
            return redirect('login');

        // Fetch all stores for the switcher
        $allStores = Store::where('user_id', $user->id)->get();

        // Determine current store
        $storeId = $request->query('store_id');
        if ($storeId) {
            $store = $allStores->where('id', $storeId)->first();
        } else {
            $store = $allStores->first();
        }

        // If no store exists (new user), show empty state or redirect to setup
        // For now, we assume the seeder ran or we handle null gracefully
        if (!$store) {
            return view('dashboard', [
                'totalRequests' => 0,
                'avgLatency' => 0,
                'errorRate' => 0,
                'activeNodes' => 0,
                'components' => [],
                'recentAlerts' => collect([]),
                'chartData' => [],
                'allStores' => $allStores,
                'currentStore' => null,
                'healthScore' => null,
                'revenueLoss' => ['loss_amount' => 0, 'loss_percentage' => 0, 'is_optimal' => true],
                'slowestRoute' => null,
                'securityResult' => ['score' => 100, 'status' => 'Secure', 'issues' => []],
                'uptime30d' => 100,
                'activeUsers' => 0,
            ]);
        }

        $now = Carbon::now();
        $twentyFourHoursAgo = $now->copy()->subHours(24);

        // 1. Top Cards Stats (Last 24 Hours)
        $stats = CheckResult::whereHas('check', fn($q) => $q->where('store_id', $store->id))
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->select(
                DB::raw('count(*) as total_requests'),
                DB::raw('avg(latency_ms) as avg_latency'),
                DB::raw("sum(case when status != 'ok' then 1 else 0 end) as error_count")
            )
            ->first();

        $totalRequests = $stats->total_requests ?? 0;
        $avgLatency = round($stats->avg_latency ?? 0);
        $errorRate = $totalRequests > 0
            ? round(($stats->error_count / $totalRequests) * 100, 2)
            : 0;

        $activeNodes = HealthCheck::where('store_id', $store->id)->where('is_active', true)->count();

        // ... (existing stats logic) ...

        // 2. Component Status (Latest result for each check)
        $components = HealthCheck::where('store_id', $store->id)
            ->with(['latestResult'])
            ->get();

        // 3. Recent Alerts
        $recentAlerts = Incident::where('store_id', $store->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 4. AI Health Score
        $healthService = new \App\Services\Scoring\HealthScoreCalculator($store);
        $healthScore = $healthService->getLatest();

        // 5. Chart Data (Requests per Hour)
        $chartData = CheckResult::whereHas('check', fn($q) => $q->where('store_id', $store->id))
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->select(
                DB::raw('EXTRACT(HOUR FROM created_at) as hour'),
                DB::raw('count(*) as count'),
                DB::raw('avg(latency_ms) as lat')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        // Normalize chart data for the view (fill missing hours with 0)
        $normalizedChart = [];
        for ($i = 0; $i < 24; $i++) {
            $h = $now->copy()->subHours(23 - $i)->hour; // 24h window ending now
            $row = $chartData->get($h);
            $normalizedChart[] = [
                'hour' => $h,
                'count' => $row->count ?? 0,
                'latency' => round($row->lat ?? 0),
                'height_pct' => ($row->count ?? 0) > 0 ? min(100, ($row->count / 150) * 100) : 5 // Visual scaling
            ];
        }

        // 6. Revenue Loss Projection (Module 3)
        $revenueService = new \App\Services\Analytics\RevenueLossCalculator();
        $monthlyRevenue = env('MONTHLY_REVENUE', 50000);
        $revenueLoss = $revenueService->calculate($avgLatency, $monthlyRevenue);

        // 7. Slowest Route (Module 4 - Phase 2)
        $slowestRoute = \Illuminate\Support\Facades\Cache::get('performance_slowest_route');

        // 8. Security & Logs (Phase 4)
        $securityScanner = new \App\Services\Security\SecurityScanner();
        $securityResult = $securityScanner->scan();

        // 9. Uptime & History (Phase 5)
        // 30-Day Uptime Calculation
        $totalChecks30d = \App\Models\CheckResult::where('created_at', '>=', now()->subDays(30))->count();
        $failedChecks30d = \App\Models\CheckResult::where('created_at', '>=', now()->subDays(30))
            ->where('status', '!=', 'ok')->count();
        $uptime30d = $totalChecks30d > 0 ? (1 - ($failedChecks30d / $totalChecks30d)) * 100 : 100;

        // 7-Day History calculation removed as it conflicts with the hourly chart view logic
        // The view expects $normalizedChart to contain hourly data from lines 98-108

        // 10. Live Traffic (Active Users) - added for SaaS
        $latestActiveUserCheck = CheckResult::whereHas('check', fn($q) => $q->where('store_id', $store->id))
            ->whereNotNull('payload->active_users')
            ->orderBy('created_at', 'desc')
            ->first();

        $activeUsers = $latestActiveUserCheck ? ($latestActiveUserCheck->payload['active_users'] ?? 0) : 0;

        // 11. AI Service Health Check (New)
        $aiHealth = \Illuminate\Support\Facades\Cache::remember('ai_service_health', 300, function () {
            try {
                return app('ai')->driver()->checkHealth();
            } catch (\Exception $e) {
                return ['status' => 'error', 'message' => $e->getMessage(), 'latency' => 0];
            }
        });

        return view('dashboard', [
            'totalRequests' => number_format($totalRequests),
            'avgLatency' => $avgLatency,
            'errorRate' => $errorRate,
            'activeNodes' => $activeNodes,
            'activeUsers' => $activeUsers,
            'components' => $components,
            'recentAlerts' => $recentAlerts,
            'healthScore' => $healthScore,
            'chartData' => $normalizedChart,
            'revenueLoss' => $revenueLoss,
            'slowestRoute' => $slowestRoute,
            'securityResult' => $securityResult,
            'uptime30d' => round($uptime30d, 2),
            'allStores' => $allStores,
            'currentStore' => $store,
            'aiHealth' => $aiHealth, // Pass AI Health Status
        ]);
    }
}
