<?php
$response = file_get_contents('output_test_ingest.txt');
$data = json_decode($response, true);
if (isset($data['message'])) {
    echo "Error Message: " . $data['message'] . "\n";
    if (isset($data['exception']))
        echo "Exception: " . $data['exception'] . "\n";
    if (isset($data['file']))
        echo "File: " . $data['file'] . " Line: " . $data['line'] . "\n";
} else {
    echo "Could not parse JSON. Check raw response.";
}
