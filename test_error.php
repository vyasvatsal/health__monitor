<?php
$url = 'http://127.0.0.1:8000/api/v1/monitor/1/capture';
$data = [
    'message' => 'Test Error via PHP Script',
    'stack_trace' => "Error: Test\n    at <anonymous>:1:1",
    'fingerprint' => 'test-error-php'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";
