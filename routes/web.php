<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/status', [App\Http\Controllers\StatusPageController::class, 'index'])->name('status');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('stores', App\Http\Controllers\StoreController::class);

    // Incidents
    Route::resource('incidents', App\Http\Controllers\IncidentController::class);

    // Settings
    Route::get('/settings/connection', [
        App\Http\Controllers\ConnectionGuideController::class,
        'index'
    ])->name('settings.connection');
    Route::get('/settings/alerts', [App\Http\Controllers\AlertSettingsController::class, 'edit'])->name('settings.alerts');
    Route::patch('/settings/alerts', [
        App\Http\Controllers\AlertSettingsController::class,
        'update'
    ])->name('settings.alerts.update');
    Route::post('/settings/alerts/test', [
        App\Http\Controllers\AlertSettingsController::class,
        'test'
    ])->name('settings.alerts.test');

    // Developer API
    Route::get('/settings/developer', [
        App\Http\Controllers\DeveloperController::class,
        'index'
    ])->name('settings.developer');

    // Global Settings (AI & SaaS)
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    // Benchmarks
    Route::get('/benchmarks', [App\Http\Controllers\StoreController::class, 'index'])->name('benchmarks.index');
    Route::get('/rum', [App\Http\Controllers\RumDashboardController::class, 'index'])->name('rum.index');
    Route::post('/rum/crawl', [App\Http\Controllers\RumDashboardController::class, 'crawl'])->name('rum.crawl');

    // Benchmarks
    Route::resource('benchmarks', App\Http\Controllers\BenchmarkController::class)
        ->only(['index', 'store', 'destroy'])
        ->parameters(['benchmarks' => 'competitor']);
    Route::post('/benchmarks/{competitor}/scan', [
        App\Http\Controllers\BenchmarkController::class,
        'scan'
    ])->name('benchmarks.scan');

    // Optimization
    Route::post('/optimization/run', [App\Http\Controllers\OptimizationController::class, 'run'])->name('optimization.run');

    // Error Monitor
    Route::get('/monitor/errors', [App\Http\Controllers\ErrorMonitorController::class, 'index'])->name('monitor.errors');

    // Tools - Image Compression
    Route::get('/tools/compression', [
        App\Http\Controllers\ImageCompressionController::class,
        'index'
    ])->name('tools.compression');
    Route::post('/tools/compression/run', [
        App\Http\Controllers\ImageCompressionController::class,
        'compress'
    ])->name('tools.compression.run');
    Route::get('/tools/compression/download/{id}', [
        App\Http\Controllers\ImageCompressionController::class,
        'download'
    ])->name('tools.compression.download');

    // AI Chat Tool
    Route::get('/tools/ai-chat', [App\Http\Controllers\AI\AIChatController::class, 'index'])->name('tools.ai-chat');
    Route::post('/tools/ai-chat/send', [
        App\Http\Controllers\AI\AIChatController::class,
        'chat'
    ])->name('tools.ai-chat.send');

    // AI Analysis (Dashboard)
    Route::post('/ai/analyze', [App\Http\Controllers\AI\AIHealthController::class, 'analyze'])->name('ai.analyze');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';