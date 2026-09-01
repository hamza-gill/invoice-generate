<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RedirectIfAuthenticated;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command('invoice:send-reminders')->dailyAt('09:00');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        /*
        |--------------------------------------------------------------------------
        | Global Middleware or Groups
        |--------------------------------------------------------------------------
        */

        // If you want to add global middleware, you can use:
        // $middleware->append(\App\Http\Middleware\YourGlobalMiddleware::class);

        /*
        |--------------------------------------------------------------------------
        | Route Middleware (aliases)
        |--------------------------------------------------------------------------
        |
        | Here we manually register middleware aliases like 'guest', 'auth', etc.
        | So we can use them in routes or controllers.
        |
        */

        $middleware->alias([
            'auth'  => \App\Http\Middleware\Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'tenant' => \App\Http\Middleware\SetTenantContext::class,
            'subscription' => \App\Http\Middleware\EnsureActiveSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
