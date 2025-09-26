<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Services\LocationService;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'profile.or.create' => \App\Http\Middleware\EnsureProfileIsComplete::class,
            'ensure.auth' => \App\Http\Middleware\EnsureUserAuthenticated::class,
        ]);
    })
    ->withSingletons([
        LocationService::class => fn () => new LocationService(),
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
