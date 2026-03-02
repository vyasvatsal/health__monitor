<?php
$host = 'http://127.0.0.1:8002'; // Use the port where we just started artisan serve
$ingestUrl = $host . '/api/ingest';
$trackUrl = $host . '/api/v1/metrics/track'; // or whatever the appropriate URL is

$storeId = 1;
$publicKey = "pk_htGRAlsL2EeVLoF1GYJuZUdt";
$secretKey = "sk_aH1nbK0bNFb4sDWVjJ7IuLWQ";
$apiKey = "live_sk_BPk4wVmh0dSZp0FXVcA8uVUN"; // using old api_key too for compat
$privateTrackingKey = "rum_2abf97dd2bcf2610e365e6c4d0de4452";

// 1. Send Error & Log Event (Uses X-Monitor-Key)
// The SDK HttpTransport sends via X-Monitor-Key => $projectKey ($api_key usually or public_key depending on config)
$ingestPayload = [
    'events' => [
        [
            'type' => 'exception',
            'message' => 'Simulated Test Exception for Verification',
            'exception' => 'Exception',
            'file' => '/path/to/simulated/file.php',
            'line' => 404,
            'level' => 'error',
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'env' => 'testing',
            'method' => 'GET',
            'url' => 'http://test.com/error-page'
        ],
        [
            'type' => 'log',
            'level' => 'info',
            'message' => 'Simulated Test Log Message for Verification',
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'env' => 'testing'
        ],
        [
            'type' => 'transaction',
            'route_name' => 'api.test.route',
            'method' => 'GET',
            'url' => 'http://test.com/api/test',
            'duration_ms' => 125,
            'memory_usage_mb' => 12.5,
            'status_code' => 200,
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'env' => 'testing'
        ],
        [
            'type' => 'health',
            'memory_usage_mb' => 15.2,
            'cpu_load' => [0.1, 0.2, 0.3],
            'db_connected' => true,
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z')
        ]
    ]
];

echo "Sending Ingest Payload to $ingestUrl...\n";
$ch = curl_init($ingestUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($ingestPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Monitor-Key: ' . $apiKey, // Since IngestController auths using this
    'X-Project-Id: ' . $storeId
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 400) {
    $errorData = json_decode($response, true);
    echo "INGEST ERROR ($httpCode): " . ($errorData['message'] ?? 'Unknown Error') . "\n";
    if (isset($errorData['file'])) {
        echo "File: " . $errorData['file'] . " Line: " . $errorData['line'] . "\n";
    }
} else {
    echo "Ingest Response ($httpCode): $response\n\n";
}


// 2. Send RUM Metrics Event
$trackPayload = [
    'url_path' => 'https://test-site.com/home',
    'device_type' => 'desktop',
    'load_time_ms' => 1500,
    'js_time_ms' => 250,
    'vitals' => [
        'lcp' => 1200,
        'cls' => 0.05,
        'fcp' => 800
    ],
    'cta_clicks' => [
        [
            'text' => 'Buy Now',
            'tag' => 'button',
            'href' => null,
            'classes' => 'btn btn-primary',
            'time' => time() * 1000
        ]
    ]
];

echo "Sending Tracking Payload to $trackUrl...\n";
$ch2 = curl_init($trackUrl);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($trackPayload));
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Private-Tracking-Key: ' . $privateTrackingKey
]);
$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);
echo "Tracker Response ($httpCode2): $response2\n";
