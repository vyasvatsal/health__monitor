<?php

use App\Models\Incident;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Incidents Module Verification...\n";

// 1. Setup User and Store
$user = User::first();
if (!$user) {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test_incident@example.com',
        'password' => Hash::make('password'),
    ]);
}
auth()->login($user);

$store = Store::where('user_id', $user->id)->first();
if (!$store) {
    if (Store::count() > 0) {
        $store = Store::first(); // Grab any store if user doesn't have one specifically (for test env) but ideally we create one
        // Attach user if needed or create new
    } else {
        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Test Store',
            'url' => 'http://example.com',
        ]);
    }
}

echo "Using User ID: {$user->id}, Store ID: {$store->id}\n";

// 2. Create Incident
echo "Creating Incident...\n";
$incidentData = [
    'store_id' => $store->id,
    'title' => 'Test Incident ' . time(),
    'description' => 'This is a test incident description.',
    'severity' => 'major',
    'status' => 'investigating',
];

$incident = Incident::create($incidentData);

if ($incident && $incident->exists) {
    echo "PASS: Incident created with ID: {$incident->id}\n";
} else {
    echo "FAIL: Incident creation failed.\n";
    exit(1);
}

// 3. Verify Database
$freshIncident = Incident::find($incident->id);
if ($freshIncident->title === $incidentData['title']) {
    echo "PASS: Incident verified in database.\n";
} else {
    echo "FAIL: Database verification failed.\n";
}

// 4. Update Status to Resolved
echo "Updating Status to Resolved...\n";
$freshIncident->update([
    'status' => 'resolved',
    'resolved_at' => now(),
]);

if ($freshIncident->status === 'resolved' && $freshIncident->resolved_at !== null) {
    echo "PASS: Incident resolved successfully.\n";
} else {
    echo "FAIL: Incident resolution failed.\n";
}

// 5. Clean up
echo "Cleaning up...\n";
$freshIncident->delete();

if (Incident::find($incident->id) === null) {
    echo "PASS: Incident deleted successfully.\n";
} else {
    echo "FAIL: Incident deletion failed.\n";
}

echo "Verification Complete.\n";
