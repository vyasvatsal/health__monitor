<?php

namespace App\Services\AI;

use App\Models\Store;
use App\Models\CheckResult;
use App\Models\ErrorLog;
use App\Models\ExecutiveSummary;
use Carbon\Carbon;

class DailyExecutiveSummaryService
{
    protected $ai;

    public function __construct()
    {
        $this->ai = app('ai')->driver();
    }

    public function generate(Store $store)
    {
        $yesterday = Carbon::yesterday();

        // 1. Gather Data for AI Context
        $stats = CheckResult::whereHas('check', fn($q) => $q->where('store_id', $store->id))
            ->whereDate('created_at', $yesterday)
            ->selectRaw('avg(latency_ms) as latency, count(*) as count, sum(case when status != "ok" then 1 else 0 end) as errors')
            ->first();

        $topErrors = ErrorLog::where('store_id', $store->id)
            ->whereDate('created_at', $yesterday)
            ->take(5)
            ->pluck('message')
            ->toArray();

        // 2. Prepare Prompt
        $prompt = "You are an expert E-commerce Store Health Analyst. 
        Generate a concise, professional executive summary for the store '{$store->name}' for {$yesterday->toFormattedDateString()}.
        
        DATA:
        - Avg Latency: " . round($stats->latency ?? 0) . "ms
        - Total Requests: {$stats->count}
        - Total Errors: " . ($stats->errors ?? 0) . "
        - Sample Error Messages: " . implode(', ', $topErrors) . "
        
        FORMAT:
        - One paragraph high-level summary.
        - 3 Bullet points of key findings (Performance, Stability, Recommendation).
        - Keep it under 200 words.";

        // 3. Call AI
        try {
            $content = $this->ai->generateText($prompt);

            // 4. Save to DB
            return ExecutiveSummary::create([
                'store_id' => $store->id,
                'summary_date' => $yesterday,
                'content' => $content,
                'metrics' => [
                    'latency' => $stats->latency,
                    'requests' => $stats->count,
                    'errors' => $stats->errors,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to generate AI Summary for Store {$store->id}: " . $e->getMessage());
            return null;
        }
    }
}
