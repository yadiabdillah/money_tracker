<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Configure standard authentication redirects
        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard'
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom exception reporting and rendering callbacks
    })->create();
