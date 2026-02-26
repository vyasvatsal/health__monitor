<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Store;

class HealthIngestTest extends TestCase
{
    public function test_health_ingestion()
    {
        $store = Store::first();
        if (!$store) {
            $this->markTestSkipped('No store found.');
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

        $response = $this->postJson('/api/ingest', $payload, [
            'X-Monitor-Key' => $store->public_key ?? $store->api_key,
            'X-Project-Id' => $store->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('health_checks', [
            'store_id' => $store->id,
            'type' => 'system',
        ]);

        $this->assertDatabaseHas('check_results', [
            'status' => 'ok',
        ]);
    }
}
