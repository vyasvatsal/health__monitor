<?php

namespace App\Services\Analytics;

class RevenueLossCalculator
{
    /**
     * Calculate potential monthly revenue loss based on latency.
     * Rule of thumb (Amazon): Every 100ms latency = 1% sales loss.
     * 
     * @param int $currentLatencyMs
     * @param float $monthlyRevenue
     * @return array
     */
    public function calculate(int $currentLatencyMs, float $monthlyRevenue): array
    {
        // Thresholds
        $targetLatency = 100; // ms (Ideal state)
        if ($currentLatencyMs <= $targetLatency) {
            return [
                'loss_amount' => 0,
                'loss_percentage' => 0,
                'is_optimal' => true
            ];
        }

        // Excess Latency
        $excessMs = $currentLatencyMs - $targetLatency;

        // Loss Calculation: 1% per 100ms excess
        // Example: 500ms total -> 400ms excess -> 4% loss
        $lossPercentage = ($excessMs / 100);

        // Cap loss percentage at 50% to be realistic
        $lossPercentage = min($lossPercentage, 50);

        // Projected Loss
        // If current revenue is $100k with 4% loss, then potential revenue was $100k / (1 - 0.04) = $104,166
        // So lost money = $4,166
        $potentialRevenue = $monthlyRevenue / (1 - ($lossPercentage / 100));
        $lossAmount = $potentialRevenue - $monthlyRevenue;

        return [
            'loss_amount' => round($lossAmount, 2),
            'loss_percentage' => round($lossPercentage, 2),
            'is_optimal' => false,
            'excess_ms' => $excessMs
        ];
    }
}
