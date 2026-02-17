<?php

require 'vendor/autoload.php';

$apiKey = 'base64:vGyx2IqKHDEybEy0y7ykCFbbfdvFP51hFnAKoFteydg='; // From .env (APP_KEY is not the API key for store, we need store API key)
// We need a valid store API key. Let's assume one exists or create a dummy one.
// Actually, TelemetryController checks 'api_key' agains Store table.

// Let's use the local API endpoint directly via Guzzle or similiar in a test script, 
// OR just use the internal code to simulate it.
// To properly test the full flow including the queue/job (if any) or controller logic:

use Illuminate\Support\Facades\Http;

$url = 'http://127.0.0.1:8000/api/v1/capture';

// First we need a store API key. 
// I'll assume the user has a store set up or I can check the database.
// For this test script, I'll just print instructions to run it or try to fetch a key if I was inside the app.
// Since this is an external script, I need to know the API key.

echo "To test, run this command in terminal (ensure you have a store with API key):\n";
echo "curl -X POST http://127.0.0.1:8000/api/v1/capture \\
-H \"Content-Type: application/json\" \\
-d '{
    \"store_id\": 1, 
    \"message\": \"Call to undefined method App\\\\User::getProfile()\",
    \"type\": \"Error\",
    \"file\": \"/var/www/html/app/Http/Controllers/UserController.php\",
    \"line\": 42,
    \"trace\": \"#0 /var/www/html/routes/web.php(15): App\\\\Http\\\\Controllers\\\\UserController->show()\\n#1 ...\"
}'";
