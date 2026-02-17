<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Alerts\AlertService;
use App\Models\CheckResult;
use Illuminate\Support\Facades\Cache;

class SendDailyDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:daily-digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a daily summary of store health to configured channels.';

    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        parent::__construct();
        $this->alertService = $alertService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Gathering daily stats...');

        // 1. Calculate Stats (Last 24h)
        $avgLatency = CheckResult::where('created_at', '>=', now()->subDay())->avg('latency_ms') ?? 0;
        $errorCount = CheckResult::where('created_at', '>=', now()->subDay())
            ->where('status', '!=', 'ok')->count();
        $totalChecks = CheckResult::where('created_at', '>=', now()->subDay())->count();

        $errorRate = $totalChecks > 0 ? ($errorCount / $totalChecks) * 100 : 0;

        // Revenue Loss (Approx)
        $monthlyRevenue = env('MONTHLY_REVENUE', 50000);
        // Simple linear approximation: 100ms excess = 1% drop? 
        // Let's use the Calculator service logic simplified here or just report latency.
        $revenueLoss = 0; // Placeholder for digest

        // Slowest Route
        $slowestRoute = Cache::get('performance_slowest_route');

        $stats = [
            'avg_latency' => round($avgLatency, 2),
            'error_rate' => round($errorRate, 2),
            'revenue_loss' => $revenueLoss, // Calculated fully in controller, simplified here
            'slowest_route' => $slowestRoute
        ];

        $this->alertService->sendDigest($stats);

        $this->info('Daily digest sent successfully.');
    }
}
