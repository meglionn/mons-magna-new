<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // No changes here — route middleware aliases are registered in Kernel in a typical Laravel app.
        // This project uses a custom bootstrap; to avoid calling non-existent methods, leave this blank.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
