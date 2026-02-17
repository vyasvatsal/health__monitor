<?php

namespace App\Http\Controllers;

use App\Models\CheckResult;
use App\Models\Incident;
use App\Models\HealthCheck;
use Illuminate\Http\Request;

class StatusPageController extends Controller
{
    public function index()
    {
        // 1. Overall System Status
        // If any check failed in the last hour, system is "Degraded"
        $failedRecent = CheckResult::where('created_at', '>=', now()->subHour())
            ->where('status', '!=', 'ok')->exists();

        $activeIncidents = Incident::where('status', '!=', 'resolved')
            ->orderBy('created_at', 'desc')->get();

        $systemStatus = 'Operational';
        if ($activeIncidents->count() > 0) {
            $systemStatus = 'Major Outage'; // Simplified logic: any incident = outage
            if ($activeIncidents->first()->severity === 'maintenance') {
                $systemStatus = 'Maintenance';
            } elseif ($activeIncidents->first()->severity === 'minor') {
                $systemStatus = 'Degraded Performance';
            }
        } elseif ($failedRecent) {
            $systemStatus = 'Degraded Performance';
        }

        // 2. Component Health (Simplified Grouping)
        // Group checks by their URL or name
        $components = HealthCheck::all()->map(function ($check) {
            $lastResult = $check->checkResults()->latest()->first();
            return [
                'name' => $check->name, // Correct column name
                'status' => $lastResult && $lastResult->status == 'ok' ? 'Operational' : 'Outage',
                'updated_at' => $lastResult ? $lastResult->created_at->diffForHumans() : 'Never',
            ];
        });

        // 3. 30-Day Uptime
        $totalChecks30d = CheckResult::where('created_at', '>=', now()->subDays(30))->count();
        $failedChecks30d = CheckResult::where('created_at', '>=', now()->subDays(30))
            ->where('status', '!=', 'ok')->count();
        $uptime30d = $totalChecks30d > 0 ? (1 - ($failedChecks30d / $totalChecks30d)) * 100 : 100;

        return view('status', [
            'systemStatus' => $systemStatus,
            'activeIncidents' => $activeIncidents,
            'components' => $components,
            'uptime30d' => round($uptime30d, 2),
            'lastUpdated' => now(),
        ]);
    }
}
