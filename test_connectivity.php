<?php

// Usage: php test_connectivity.php <api_key> [endpoint_type] [base_url]

$apiKey = $argv[1] ?? null;
$type = $argv[2] ?? 'telemetry'; // telemetry, ai, optimize
$baseUrl = $argv[3] ?? 'http://127.0.0.1:8000';

if (!$apiKey) {
    echo "\nError: API Key is required.\n";
    echo "Usage: php test_connectivity.php <api_key> [telemetry|ai|optimize] [base_url]\n";
    echo "Example: php test_connectivity.php live_sk_123 telemetry http://localhost:8000\n\n";
    exit(1);
}

// 1. Configure Endpoint and Payload
$url = rtrim($baseUrl, '/');
$payload = [];

switch ($type) {
    case 'telemetry':
        $url .= '/api/v1/telemetry';
        $p = [
            'api_key' => $apiKey,
            'checks' => [
                [
                    'name' => 'CLI Connectivity Test',
                    'type' => 'manual',
                    'status' => 'ok',
                    'latency' => rand(10, 50),
                    'payload' => [
                        'source' => 'cli_script',
                        'timestamp' => time(),
                        'active_users' => rand(5, 100) // Simulate Live Traffic
                    ]
                ]
            ]
        ];
        $payload = json_encode($p);
        break;

    case 'ai':
        $url .= '/api/v1/ai/analyze';
        // AI endpoint needs store_data. In a real scenario this comes from the frontend or a job.
        // We'll mock a request that looks like it came from the dashboard.
        $p = [
            'store_data' => [
                'store_name' => 'CLI Test Store',
                'score' => rand(60, 90),
                'performance_score' => rand(70, 95),
                'ux_score' => rand(60, 90),
                'trust_score' => rand(80, 100),
                'seo_score' => rand(50, 80),
                'issues' => ['High Latency', 'Low Conversion'],
                'recent_alerts' => ['Database CPU High'],
            ]
        ];
        $payload = json_encode($p);
        break;

    case 'optimize':
        $url .= '/api/v1/optimization/run';
        // Optimization doesn't need a specific payload, just authentication (which currently uses session/auth)
        // BUT, our API doesn't use token auth for this endpoint in web.php, it uses session. 
        // Wait, the user wants to use this via API. 
        // The implementation_plan said we'd update the controller to return JSON.
        // However, middleware 'auth' is still on the route in web.php. 
        // For CLI testing without login cookie, this might fail with 401 or redirect to login.
        // Let's assume for this test we might get a redirect to login if not authenticated.
        // To properly test API access to optimization, we'd need an API token route, but it's defined in web.php protected by auth.
        // Let's TRY it. If it fails (redirects to login), we know why.
        $payload = json_encode([]);
        break;
}

echo "--------------------------------------------------\n";
echo "Health Monitor Connectivity Tester\n";
echo "--------------------------------------------------\n";
echo "Type        : {$type}\n";
echo "Target URL  : {$url}\n";
echo "API Key     : {$apiKey} (Used primarily for telemetry)\n";
echo "Sending Packet... ";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'User-Agent: HealthMonitorCLI/1.0',
    // 'Authorization: Bearer ' . $apiKey // Only if using Sanctum
]);

// If testing AI, we might need a bearer token if it was redundant with session, but currently AI Controller doesn't enforce auth middleware in api.php? 
// Let's check api.php again. 
// Route::post('/ai/analyze', ...)->middleware('subscribed:pro');
// Checks user()->isPro(). 
// If we call api/v1/ai/analyze, it expects a logged in user via session OR proper API auth.
// Since api.php usually uses 'auth:sanctum', but here it just says 'subscribed:pro'.
// 'subscribed' middleware checks $request->user().
// Without a token, $request->user() is null.
// Use existing API key to resolve user? 
// The TelemetryController resolves user via API Key lookup manually.
// The AIController does NOT. It relies on $request->user().
// We might need to adjust AIController to resolve user from API Key if not logged in, OR basic auth. 
// For now, let's just send the request and see the result.

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo "FAILED\n";
    echo "Error: {$curlError}\n";
    exit(1);
}

echo "DONE\n";
echo "--------------------------------------------------\n";
echo "HTTP Status : {$httpCode}\n";
// echo "Response    : {$response}\n"; 
// Pretty print response if JSON
$json = json_decode($response, true);
if ($json) {
    echo "Response    : " . json_encode($json, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Response    : " . substr($response, 0, 500) . "...\n";
}
echo "--------------------------------------------------\n";

if ($httpCode === 200) {
    echo "\n✅ SUCCESS: Connection established.\n";
    if ($type === 'telemetry') {
        echo "Check your dashboard. Active Users count should update.\n";
    }
} elseif ($httpCode === 401 || $httpCode === 403) {
    echo "\n❌ FAILED: Authentication/Authorization Error.\n";
    echo "Note: AI/Optimization endpoints might require session auth or 'Pro' tier.\n";
} else {
    echo "\n❌ FAILED: Server returned an error.\n";
}
echo "\n";
