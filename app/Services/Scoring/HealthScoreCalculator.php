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
        // In a real scenario, this would aggregate data from:
        // - Performance (Lighthouse/PageSpeed)
        // - UX (Accessibility/Layout Shift)
        // - Conversion (Sales/Funnel data)
        // - Trust (Reviews/Sentiment)

        // For now, we simulate these metrics
        $metrics = [
            'performance' => rand(85, 98),
            'ux' => rand(90, 100),
            'conversion' => rand(70, 95),
            'trust' => rand(95, 100),
        ];

        // Weighted Average
        // Performance: 30%, UX: 25%, Conversion: 30%, Trust: 15%
        $scoreValue = (
            ($metrics['performance'] * 0.30) +
            ($metrics['ux'] * 0.25) +
            ($metrics['conversion'] * 0.30) +
            ($metrics['trust'] * 0.15)
        );

        // Determine trend (simplified for now)
        $lastScore = HealthScore::where('store_id', $this->store->id)->latest()->first();
        $trend = 'stable';

        if ($lastScore) {
            if ($scoreValue > $lastScore->score) {
                $trend = 'up';
            } elseif ($scoreValue < $lastScore->score) {
                $trend = 'down';
            }
        }

        return HealthScore::create([
            'store_id' => $this->store->id,
            'score' => round($scoreValue, 1),
            'metrics_json' => json_encode($metrics),
            'trend' => $trend,
        ]);
    }

    public function getLatest()
    {
        return HealthScore::where('store_id', $this->store->id)->latest()->first() ?? $this->calculate();
    }
}
