<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: [
            '*',
        ]);
        $middleware->alias([
            'subscribed' => \App\Http\Middleware\CheckSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

/*
|--------------------------------------------------------------------------
| Vercel Configuration
|--------------------------------------------------------------------------
|
| When running on Vercel, the filesystem is read-only. We need to use
| the /tmp directory for storage, specifically for views, cache, and
| sessions. We also ensure the directory structure exists.
|
*/
if (isset($_ENV['VERCEL'])) {
    $app->useStoragePath('/tmp/storage');

    if (!is_dir(storage_path())) {
        mkdir(storage_path(), 0777, true);
    }

    // Ensure critical directories exist
    $directories = [
        storage_path('framework/views'),
        storage_path('framework/cache/data'),
        storage_path('framework/sessions'),
        storage_path('logs'),
    ];

    foreach ($directories as $directory) {
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }
}

return $app;
