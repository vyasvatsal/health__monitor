<?php

namespace App\Services\AI;

use App\Models\Store;
use App\Models\ExecutiveSummary;
use App\Models\HealthScore;
use App\Models\CheckResult;
use App\Models\StoreAlert;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DailyExecutiveSummaryService
{
    /**
     * Generate 24h summary for a store.
     *
     * @param Store $store
     * @return ExecutiveSummary|null
     */
    public function generate(Store $store)
    {
        $now = now();
        $start = $now->copy()->subHours(24);

        // 1. Gather Metrics Snapshot
        $metrics = $this->gatherMetrics($store, $start, $now);

        // 2. Build Context for AI
        $context = $this->buildAIContext($store, $metrics);

        // 3. Call AI Engine (AIManager/Gemini/Grok)
        $aiContent = $this->generateAIContent($context);

        if (!$aiContent) {
            Log::error("Failed to generate AI content for Executive Summary - Store #{$store->id}");
            return null;
        }

        // 4. Create record
        return $store->executiveSummaries()->create([
            'content' => $aiContent,
            'metrics_snapshot' => $metrics,
            'period_start' => $start,
            'period_end' => $now,
        ]);
    }

    protected function gatherMetrics(Store $store, Carbon $start, Carbon $end)
    {
        $stats = CheckResult::whereHas('check', fn($q) => $q->where('store_id', $store->id))
            ->whereBetween('created_at', [$start, $end])
            ->select(
                \Illuminate\Support\Facades\DB::raw('avg(latency_ms) as avg_latency'),
                \Illuminate\Support\Facades\DB::raw("sum(case when status != 'ok' then 1 else 0 end) as error_count"),
                \Illuminate\Support\Facades\DB::raw('count(*) as total_requests')
            )
            ->first();

        $alertsCount = StoreAlert::where('store_id', $store->id)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $latestScore = HealthScore::where('store_id', $store->id)->latest()->first();

        return [
            'avg_latency' => round($stats->avg_latency ?? 0, 2),
            'error_rate' => $stats->total_requests > 0 ? round(($stats->error_count / $stats->total_requests) * 100, 2) : 0,
            'total_alerts' => $alertsCount,
            'current_score' => $latestScore->score ?? 'N/A',
        ];
    }

    protected function buildAIContext(Store $store, array $metrics)
    {
        return "Store: {$store->name}
        Period: Last 24 Hours
        
        METRICS:
        - Health Score: {$metrics['current_score']}
        - Avg Latency: {$metrics['avg_latency']}ms
        - Error Rate: {$metrics['error_rate']}%
        - New Alerts: {$metrics['total_alerts']}
        
        Analyze this data and provide a professional, concise executive summary for the store owner. 
        Focus on stability, performance trends, and any potential revenue risks.
        Return 2-3 short paragraphs in Markdown.";
    }

    protected function generateAIContent(string $context)
    {
        try {
            $systemPrompt = "You are a professional Site Reliability Engineer and Business Analyst. 
            Your goal is to provide a 'Morning Digest' for an e-commerce store owner. 
            Be concise, authoritative, and helpful. Use Markdown for formatting.";

            // Use the integrated AIManager
            $response = app('ai')->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $context],
            ]);

            return $response['content'] ?? null;
        } catch (\Throwable $e) {
            Log::error("AI Executive Summary Generation Error: " . $e->getMessage());
            return null;
        }
    }
}
