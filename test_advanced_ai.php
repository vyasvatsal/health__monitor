<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;
use App\Models\ErrorGroup;

try {
    echo "Sending advanced request...\n";

    // Simulate a Database Connection Error which is common and good for AI analysis
    $message = 'SQLSTATE[HY000] [2002] Connection refused (SQL: select * from `users` where `email` = \'admin@example.com\' limit 1)';
    $trace = '#0 /var/www/html/vendor/laravel/framework/src/Illuminate/Database/Connectors/Connector.php(70): PDO->__construct(\'mysql:host=127....\', \'root\', \'secret\', Array)
#1 /var/www/html/vendor/laravel/framework/src/Illuminate/Database/Connectors/Connector.php(46): Illuminate\Database\Connectors\Connector->createPdoConnection(\'mysql:host=127....\', \'root\', \'secret\', Array)
#2 /var/www/html/vendor/laravel/framework/src/Illuminate/Database/Connectors/MySqlConnector.php(24): Illuminate\Database\Connectors\Connector->createConnection(\'mysql:host=127....\', Array, Array)
#3 ...';

    $response = Http::post('http://127.0.0.1:8000/api/v1/capture', [
        'store_id' => 1,
        'message' => $message,
        'type' => 'PDOException',
        'file' => '/var/www/html/vendor/laravel/framework/src/Illuminate/Database/Connectors/Connector.php',
        'line' => 70,
        'trace' => $trace
    ]);

    echo "Response status: " . $response->status() . "\n";

    // Check DB
    $group = ErrorGroup::latest('updated_at')->first();
    if ($group) {
        echo "Latest Group ID: " . $group->id . "\n";
        echo "Fingerprint: " . $group->fingerprint . "\n";

        if ($group->ai_analysis) {
            echo "AI Analysis found:\n";
            print_r($group->ai_analysis);
        } else {
            echo "AI Analysis found: No (Might be null or failed)\n";
        }
    } else {
        echo "No error group found.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
