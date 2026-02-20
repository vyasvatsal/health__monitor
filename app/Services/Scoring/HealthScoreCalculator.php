<?php

namespace App\Services\Scoring;

use App\Models\HealthScore;
use Illuminate\Support\Facades\Log;

class HealthScoreCalculator
{
    protected $store;

    public function __construct(\App\Models\Store $store)
    {
        $this->store = $store;
    }

    /**
     * Calculate and save the current health score.
     * 
     * @return HealthScore
     */
    public function calculate(): HealthScore
    {
        // 1. Fetch Real Data
        $now = now();
        $twentyFourHoursAgo = $now->copy()->subHours(24);

        // A. Performance Data (Latency & Errors)
        $stats = \App\Models\CheckResult::whereHas('check', fn($q) => $q->where('store_id', $this->store->id))
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->select(
                \Illuminate\Support\Facades\DB::raw('avg(latency_ms) as avg_latency'),
                \Illuminate\Support\Facades\DB::raw("sum(case when status != 'ok' then 1 else 0 end) as error_count"),
                \Illuminate\Support\Facades\DB::raw('count(*) as total_requests')
            )
            ->first();

        $avgLatency = $stats->avg_latency ?? 0;
        $totalRequests = $stats->total_requests ?? 0;
        $errorRate = $totalRequests > 0 ? ($stats->error_count / $totalRequests) * 100 : 0;

        // B. Incidents (Trust Penalty)
        $activeIncidents = \App\Models\Incident::where('store_id', $this->store->id)
            ->where('status', '!=', 'resolved')
            ->count();

        // C. Security Score (Trust Base)
        $securityScanner = new \App\Services\Security\SecurityScanner();
        $securityResult = $securityScanner->scan(); // Returns array with 'score'
        $securityScore = $securityResult['score'] ?? 100;

        // D. Revenue Impact (Conversion Proxy)
        $revenueService = new \App\Services\Analytics\RevenueLossCalculator();
        $revenueLoss = $revenueService->calculate($avgLatency, 50000); // Default $50k revenue

        // --- CALCULATION LOGIC ---

        // 1. Performance Score (30%)
        // Base 100.
        // Penalty: -1 for every 50ms > 100ms
        // Penalty: -10 for error rate > 1%
        $perfScore = 100;
        if ($avgLatency > 100) {
            $perfScore -= floor(($avgLatency - 100) / 50);
        }
        if ($errorRate > 1) {
            $perfScore -= 10;
        }
        if ($errorRate > 5) {
            $perfScore -= 20; // Heavy penalty for high error rates
        }
        $perfScore = max(0, min(100, $perfScore));

        // 2. UX Score (25%)
        // Proxied by Performance (Speed) & Stability (Error Rate)
        // High latency = Bad UX. High errors = Bad UX.
        $uxScore = 100;
        if ($avgLatency > 200)
            $uxScore -= 10;
        if ($avgLatency > 500)
            $uxScore -= 20;
        if ($errorRate > 0.5)
            $uxScore -= 15;
        $uxScore = max(0, min(100, $uxScore));

        // 3. Conversion Score (30%)
        // Directly tied to Revenue Loss. 
        // If is_optimal, 100. Else, deduct based on loss percentage.
        $convScore = 100;
        if (!$revenueLoss['is_optimal']) {
            // Loss percentage (e.g., 7%) * 2 = 14 point deduction
            $deduction = ($revenueLoss['loss_percentage'] ?? 0) * 2;
            $convScore -= $deduction;
        }
        $convScore = max(50, min(100, $convScore)); // Floor at 50 to not be too harsh

        // 4. Trust Score (15%)
        // Base: Security Score
        // Penalty: Active Incidents
        $trustScore = $securityScore;
        if ($activeIncidents > 0) {
            $trustScore -= ($activeIncidents * 10);
        }
        $trustScore = max(0, min(100, $trustScore));


        // --- FINAL WEIGHTED AVERAGE ---
        $scoreValue = (
            ($perfScore * 0.30) +
            ($uxScore * 0.25) +
            ($convScore * 0.30) +
            ($trustScore * 0.15)
        );

        // Trend Logic
        $lastScore = HealthScore::where('store_id', $this->store->id)->latest()->first();
        $trend = 'stable';
        if ($lastScore) {
            if ($scoreValue > $lastScore->score)
                $trend = 'up';
            elseif ($scoreValue < $lastScore->score)
                $trend = 'down';
        }

        $metrics = [
            'performance' => round($perfScore),
            'ux' => round($uxScore),
            'conversion' => round($convScore),
            'trust' => round($trustScore),
        ];

        return HealthScore::create([
            'store_id' => $this->store->id,
            'score' => round($scoreValue, 1),
            'metrics_json' => $metrics,
            'trend' => $trend,
            'metric_availability' => $trustScore,
            'metric_performance' => $perfScore,
            'metric_incidents' => $activeIncidents,
            'recorded_at' => now(),
        ]);
    }

    public function getLatest()
    {
        $latest = HealthScore::where('store_id', $this->store->id)->latest()->first();

        // If no score exists, or if the latest score is older than 1 hour, recalculate.
        if (!$latest || $latest->created_at->lt(now()->subHour())) {
            return $this->calculate();
        }

        return $latest;
    }
}
