<?php

namespace App\Jobs;

use App\Models\PageMetrics;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUrlGradeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $metric;

    /**
     * Create a new job instance.
     */
    public function __construct(PageMetrics $metric)
    {
        $this->metric = $metric;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $vitals = $this->metric->vitals ?? [];
        $loadTime = $this->metric->load_time_ms;

        $score = 100;

        // Deduction: Load Time
        // Anything under 1000ms is perfect (100).
        if ($loadTime) {
            if ($loadTime > 1000 && $loadTime <= 2500) {
                $score -= 10;
            } elseif ($loadTime > 2500 && $loadTime <= 4000) {
                $score -= 25;
            } elseif ($loadTime > 4000) {
                $score -= 50;
            }
        }

        // Deduction: Largest Contentful Paint (LCP)
        // Good: <= 2500ms | Needs Improvement: <= 4000ms | Poor: > 4000ms
        $lcp = $vitals['lcp'] ?? null;
        if ($lcp) {
            if ($lcp > 2500 && $lcp <= 4000) {
                $score -= 10;
            } elseif ($lcp > 4000) {
                $score -= 30;
            }
        }

        // Deduction: Cumulative Layout Shift (CLS)
        // Good: <= 0.1 | Needs Improvement: <= 0.25 | Poor: > 0.25
        $cls = $vitals['cls'] ?? null;
        if ($cls) {
            if ($cls > 0.1 && $cls <= 0.25) {
                $score -= 10;
            } elseif ($cls > 0.25) {
                $score -= 30;
            }
        }

        // Convert 0-100 Score to Grade
        $grade = 'F';
        if ($score >= 90) {
            $grade = 'A';
        } elseif ($score >= 80) {
            $grade = 'B';
        } elseif ($score >= 70) {
            $grade = 'C';
        } elseif ($score >= 60) {
            $grade = 'D';
        }

        // Save Grade
        $this->metric->grade = $grade;
        $this->metric->save();
    }
}
