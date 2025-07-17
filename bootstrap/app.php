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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            // Web middleware group - removed EnsureCleanJsonResponse as it was causing output issues
        ]);
        
        // Add session middleware to API routes for authenticated endpoints
        $middleware->api(append: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // Note: EnsureCleanJsonResponse removed from global middleware
        ]);
        
        // Ensure CSRF verification is enabled for web routes
        $middleware->validateCsrfTokens(except: [
            // Add any routes you want to exclude from CSRF here
            'api/keywords/*', // Allow CSRF-free access to keyword API for demo mode
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
