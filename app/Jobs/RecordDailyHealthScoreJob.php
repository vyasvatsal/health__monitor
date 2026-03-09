<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecordDailyHealthScoreJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $stores = \App\Models\Store::all();

        foreach ($stores as $store) {
            try {
                $calculator = new \App\Services\Scoring\HealthScoreCalculator($store);
                $calculator->calculate(true); // true = is_daily_snapshot
            } catch (\Exception $e) {
                \Log::error("Failed to record daily health score for store #{$store->id}: " . $e->getMessage());
            }
        }
    }
}
