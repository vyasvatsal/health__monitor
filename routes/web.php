<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/debug-deploy', function () {
    return [
        'base_path' => base_path(),
        'resource_path' => resource_path(),
        'resource_views_path' => resource_path('views'),
        'resource_views_exists' => is_dir(resource_path('views')),
        'storage_path' => storage_path(),
        'storage_framework_views_exists' => is_dir(storage_path('framework/views')),
        'views_files' => is_dir(resource_path('views')) ? scandir(resource_path('views')) : 'NOT_FOUND',
        'env_vercel' => $_ENV['VERCEL'] ?? 'NOT_SET',
    ];
});

Route::get('/', function () {
    return view('welcome');
});

// Public Status Page (Phase 6)
Route::get('/status', [App\Http\Controllers\StatusPageController::class, 'index'])->name('status');

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/test-sdk', function () {
    \Illuminate\Support\Facades\Log::warning('This is a test warning triggered from the web route to test SDK logs');
    throw new \Exception('This is a hard crash triggered from the web route to test the SDK Exception Handler');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('stores', App\Http\Controllers\StoreController::class);
    // Incidents (Phase 5)
    Route::resource('incidents', App\Http\Controllers\IncidentController::class);

    // Settings
    Route::get('/settings/alerts', [App\Http\Controllers\AlertSettingsController::class, 'edit'])->name('settings.alerts');
    Route::patch('/settings/alerts', [App\Http\Controllers\AlertSettingsController::class, 'update'])->name('settings.alerts.update');
    Route::post('/settings/alerts/test', [App\Http\Controllers\AlertSettingsController::class, 'test'])->name('settings.alerts.test');

    // Developer API (Phase 7)
    Route::get('/settings/developer', [App\Http\Controllers\DeveloperController::class, 'index'])->name('settings.developer');

    // Global Settings (AI & SaaS)
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    // Benchmarks
    Route::resource('benchmarks', App\Http\Controllers\BenchmarkController::class)
        ->only(['index', 'store', 'destroy'])
        ->parameters(['benchmarks' => 'competitor']);
    Route::post('/benchmarks/{competitor}/scan', [App\Http\Controllers\BenchmarkController::class, 'scan'])->name('benchmarks.scan');

    // Optimization
    Route::post('/optimization/run', [App\Http\Controllers\OptimizationController::class, 'run'])->name('optimization.run');

    // Error Monitor
    Route::get('/monitor/errors', [App\Http\Controllers\ErrorMonitorController::class, 'index'])->name('monitor.errors');

    // Tools - Image Compression
    Route::get('/tools/compression', [App\Http\Controllers\ImageCompressionController::class, 'index'])->name('tools.compression');
    Route::post('/tools/compression/run', [App\Http\Controllers\ImageCompressionController::class, 'compress'])->name('tools.compression.run');
    Route::get('/tools/compression/download/{id}', [App\Http\Controllers\ImageCompressionController::class, 'download'])->name('tools.compression.download');

    // AI Chat Tool
    Route::get('/tools/ai-chat', [App\Http\Controllers\AI\AIChatController::class, 'index'])->name('tools.ai-chat');
    Route::post('/tools/ai-chat/send', [App\Http\Controllers\AI\AIChatController::class, 'chat'])->name('tools.ai-chat.send');

    // AI Analysis (Dashboard)
    Route::post('/ai/analyze', [App\Http\Controllers\AI\AIHealthController::class, 'analyze'])->name('ai.analyze');

    // AI Analysis (Dashboard)
    Route::post('/ai/analyze', [App\Http\Controllers\AI\AIHealthController::class, 'analyze'])->name('ai.analyze');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
