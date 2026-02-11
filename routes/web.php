<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('stores', App\Http\Controllers\StoreController::class);
    Route::resource('incidents', App\Http\Controllers\IncidentController::class)->only(['index', 'show', 'update']);

    // Settings
    Route::get('/settings/alerts', [App\Http\Controllers\AlertSettingsController::class, 'edit'])->name('settings.alerts');
    Route::patch('/settings/alerts', [App\Http\Controllers\AlertSettingsController::class, 'update'])->name('settings.alerts.update');

    // Global Settings (AI & SaaS)
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    // Benchmarks
    Route::resource('benchmarks', App\Http\Controllers\BenchmarkController::class)
        ->only(['index', 'store', 'destroy'])
        ->parameters(['benchmarks' => 'competitor']);
    Route::post('/benchmarks/{competitor}/scan', [App\Http\Controllers\BenchmarkController::class, 'scan'])->name('benchmarks.scan');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
