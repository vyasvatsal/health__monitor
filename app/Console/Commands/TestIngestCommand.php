<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Store;
use App\Models\HealthCheck;
use App\Models\CheckResult;

class TestIngestCommand extends Command
{
    protected $signature = 'test:ingest';
    protected $description = 'Test health ingestion logic locally';

    public function handle()
    {
        $store = Store::first();
        if (!$store) {
            $this->error('No store found');
            return;
        }

        $payload = [
            'events' => [
                [
                    'type' => 'health',
                    'timestamp' => now()->toISOString(),
                    'memory_usage_mb' => 25.5,
                    'cpu_load' => 1.2,
                    'db_connected' => true,
                ]
            ]
        ];

        $req = \Illuminate\Http\Request::create('/api/ingest', 'POST', [], [], [], [
            'HTTP_X-Monitor-Key' => $store->public_key ?? $store->api_key,
            'HTTP_X-Project-Id' => $store->id,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        $response = app()->handle($req);

        $this->info("Status: " . $response->getStatusCode());
        $this->info("Content: " . $response->getContent());
        $this->info("HealthChecks: " . HealthCheck::count());
        $this->info("CheckResults: " . CheckResult::count());
    }
}
