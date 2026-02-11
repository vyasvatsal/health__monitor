<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Store;
use App\Models\HealthScore;
use App\Services\Scoring\HealthScoreCalculator;
use Carbon\Carbon;

class CalculateDailyScores extends Command
{
    protected $signature = 'scores:calculate';
    protected $description = 'Calculate daily health scores for all stores';

    public function handle(HealthScoreCalculator $calculator)
    {
        $stores = Store::all();
        $today = Carbon::today();

        $this->info("Calculating scores for " . $stores->count() . " stores...");

        foreach ($stores as $store) {
            $metrics = $calculator->calculate($store);

            HealthScore::updateOrCreate(
                [
                    'store_id' => $store->id,
                    'recorded_at' => $today,
                ],
                [
                    'score' => $metrics['total'] ?? 0,
                    'metric_availability' => $metrics['availability'] ?? 0,
                    'metric_performance' => $metrics['performance'] ?? 0,
                    'metric_incidents' => $metrics['incidents'] ?? 0,
                ]
            );

            $this->info("Store [{$store->name}]: " . ($metrics['total'] ?? 0) . "/100");
        }

        $this->info('Done.');
    }
}
