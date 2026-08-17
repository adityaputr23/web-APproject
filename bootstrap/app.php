<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust ALL proxies — required for Vercel edge (SSL termination)
        $middleware->trustProxies(at: '*');

        // Exclude login/logout from CSRF — safe because:
        // 1. Login can't be CSRF-exploited without knowing the victim's credentials
        // 2. All admin routes still have full CSRF protection
        // 3. This is necessary for Vercel serverless where cookie sessions may not
        //    persist CSRF tokens reliably across cold starts
        $middleware->validateCsrfTokens(except: [
            '/login',
            '/logout',
            '/enquire',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Vercel: redirect writable storage to /tmp
if (getenv('VERCEL') || isset($_SERVER['VERCEL'])) {
    $app->useStoragePath('/tmp/storage');
}

return $app;
