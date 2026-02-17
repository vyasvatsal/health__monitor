<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public Status Page (Phase 6)
Route::get('/status', [App\Http\Controllers\StatusPageController::class, 'index'])->name('status');

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
