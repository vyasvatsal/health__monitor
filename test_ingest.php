<?php

require __DIR__ . '/vendor/autoload.php';

// 1. Setup: Get a valid API Key from DB
$pdo = new PDO('mysql:host=127.0.0.1;dbname=health_monitor', 'root', '');
$stmt = $pdo->query("SELECT api_key FROM stores LIMIT 1");
$apiKey = $stmt->fetchColumn();

if (!$apiKey) {
    die("Error: No stores found. Run seeds first.\n");
}

echo "Using API Key: {$apiKey}\n";

// 2. Prepare Payload (Simulating an Agent)
$payload = [
    'api_key' => $apiKey,
    'checks' => [
        [
            'name' => 'Test DB Connection',
            'type' => 'database',
            'status' => 'ok',
            'latency' => 15
        ],
        [
            'name' => 'Critical API Failure',
            'type' => 'http',
            'status' => 'critical',
            'latency' => 5000,
            'payload' => ['error' => 'Connection refused']
        ]
    ]
];

// 3. Send Request
$ch = curl_init('http://localhost:8000/api/v1/telemetry');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 4. Output Result
echo "Response Code: {$httpCode}\n";
echo "Response Body: {$response}\n";

if ($httpCode === 200) {
    echo "\nSUCCESS: Telemetry ingested.\n";
} else {
    echo "\nFAILED: Check logs.\n";
}
