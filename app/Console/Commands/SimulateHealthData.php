<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Store;
use App\Models\HealthCheck;
use App\Models\CheckResult;
use App\Models\Incident;
use App\Models\User;
use Carbon\Carbon;

class SimulateHealthData extends Command
{
    protected $signature = 'simulate:health-data {--clean : Wipe existing data before seeding}';
    protected $description = 'Generates realistic historical health data for the dashboard demo.';

    public function handle()
    {
        if ($this->option('clean')) {
            $this->info('Cleaning old data...');
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
            CheckResult::truncate();
            Incident::truncate();
            HealthCheck::truncate();
            Store::truncate();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        }

        // 1. Ensure a Store exists for the first user
        $user = User::first();
        if (!$user) {
            $this->error('No users found. Please register a user first.');
            return;
        }

        $store = Store::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'Flagship Store (Production)',
                'domain' => 'store.production.com',
                'api_key' => 'live_sk_' . \Str::random(24),
                'tier' => 'enterprise'
            ]
        );

        $this->info("Simulating data for store: {$store->name}");

        // 2. Create Health Checks
        $checks = [
            ['name' => 'MySQL Database', 'type' => 'database', 'config' => ['threshold' => 100]],
            ['name' => 'Redis Cache', 'type' => 'redis', 'config' => ['threshold' => 50]],
            ['name' => 'Checkout API', 'type' => 'http', 'config' => ['endpoint' => '/api/checkout']],
            ['name' => 'Search Service', 'type' => 'elasticsearch', 'config' => ['nodes' => 3]],
        ];

        $checkInstances = [];
        foreach ($checks as $c) {
            $checkInstances[] = HealthCheck::firstOrCreate(
                ['store_id' => $store->id, 'name' => $c['name']],
                $c
            );
        }

        // 3. Generate 24 Hours of Data (5-minute intervals)
        $now = Carbon::now();
        $startTime = $now->copy()->subHours(24);
        $totalPoints = 0;

        $this->withProgressBar(range(0, 24 * 12), function ($i) use ($startTime, $checkInstances, &$totalPoints) {
            $currentTime = $startTime->copy()->addMinutes($i * 5);

            // Introduce some "noise" pattern (high load during day, low at night)
            $hour = $currentTime->hour;
            $isPeak = ($hour >= 9 && $hour <= 18);

            foreach ($checkInstances as $check) {
                // Randomize status based on probability
                $isFailure = (rand(1, 200) == 1); // 0.5% chance of failure

                $latency = $isPeak ? rand(40, 150) : rand(10, 60);
                if ($isFailure)
                    $latency = rand(1000, 5000);

                CheckResult::create([
                    'health_check_id' => $check->id,
                    'status' => $isFailure ? 'critical' : 'ok',
                    'latency_ms' => $latency,
                    'payload' => ['memory_usage' => rand(20, 80) . '%'],
                    'created_at' => $currentTime
                ]);
                $totalPoints++;
            }
        });

        $this->newLine();
        $this->info("Generated {$totalPoints} data points.");

        // 4. Create some Incidents
        Incident::firstOrCreate(
            ['store_id' => $store->id, 'title' => 'Unexpected Latency Spike'],
            [
                'severity' => 'warning',
                'status' => 'resolved',
                'description' => 'Detected 500ms+ latency on Checkout API.',
                'created_at' => $now->copy()->subHours(5),
                'resolved_at' => $now->copy()->subHours(4),
            ]
        );

        // Active incident
        Incident::firstOrCreate(
            ['store_id' => $store->id, 'title' => 'Search Index Out of Sync'],
            [
                'severity' => 'critical',
                'status' => 'open',
                'description' => 'Elasticsearch failed to index last 500 products.',
                'created_at' => $now->copy()->subMinutes(30),
            ]
        );

        $this->info('Simulation complete. You should see data on the dashboard now.');
    }
}
