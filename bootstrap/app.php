<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register custom middleware
        $middleware->alias([
            'check.activity'      => \App\Http\Middleware\CheckUserActivity::class,
            'role'                => \App\Http\Middleware\CheckRole::class,
            'can-modify'          => \App\Http\Middleware\CheckCanModify::class,
            'erp.header.validate' => \App\Http\Middleware\ValidationERPToken::class,
            'gateway.validate'    => \App\Http\Middleware\GatewayValidation::class,
        ]);
        
        // Apply middleware to web routes
        $middleware->web([
            \App\Http\Middleware\CheckUserActivity::class,
            \App\Http\Middleware\RedirectBackToERPMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
