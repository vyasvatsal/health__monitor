<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Store;

class TestTelemetry extends Command
{
    protected $signature = 'test:telemetry';
    protected $description = 'Sends a test telemetry payload to the local API';

    public function handle()
    {
        $this->info('DB Host: ' . config('database.connections.mysql.host'));
        $this->info('DB Port: ' . config('database.connections.mysql.port'));

        $store = Store::first();
        if (!$store) {
            $this->error('No store found. Run migrations/seeds first.');
            return;
        }

        $this->info("Using Store: {$store->name} (Key: {$store->api_key})");

        $response = Http::post('http://localhost:8000/api/v1/telemetry', [
            'api_key' => $store->api_key,
            'checks' => [
                [
                    'name' => 'Artisan Integration Test',
                    'type' => 'cli',
                    'status' => 'ok',
                    'latency' => rand(10, 50)
                ],
                [
                    'name' => 'Critical Test Limit',
                    'type' => 'cli',
                    'status' => 'critical',
                    'latency' => 9999,
                    'payload' => ['reason' => 'Testing incident creation']
                ]
            ]
        ]);

        $this->info("Status: " . $response->status());
        $this->info("Body: " . $response->body());

        if ($response->successful()) {
            $this->info('SUCCESS: Telemetry ingested.');
        } else {
            $this->error('FAILED: Check logs.');
        }
    }
}
