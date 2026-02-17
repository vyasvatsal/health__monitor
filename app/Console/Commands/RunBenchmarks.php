<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Competitor;
use App\Models\Store;
use App\Services\Benchmarking\BenchmarkRunner;
use Illuminate\Support\Facades\Log;

class RunBenchmarks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:run-benchmarks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run performance benchmarks for all configured competitors.';

    protected $runner;

    public function __construct(BenchmarkRunner $runner)
    {
        parent::__construct();
        $this->runner = $runner;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automated benchmarks...');

        $competitors = Competitor::with('store')->get();

        if ($competitors->isEmpty()) {
            $this->info('No competitors found to benchmark.');
            return;
        }

        foreach ($competitors as $competitor) {
            $this->info("Benchmarking: {$competitor->name} ({$competitor->url})");
            try {
                $result = $this->runner->run($competitor->store, $competitor);
                $this->info("  -> Winner: " . strtoupper($result->winner));
            } catch (\Exception $e) {
                Log::error("Benchmark failed for {$competitor->name}: " . $e->getMessage());
                $this->error("  -> Failed. See logs.");
            }
        }

        $this->info('All benchmarks completed.');
    }
}
