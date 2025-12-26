<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\Handler;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.rate_limit' => \App\Http\Middleware\ApiRateLimitMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Créer une instance du Handler
        $handler = new Handler();
        
        // Render uniquement pour les erreurs 500
        $exceptions->render(function (\Throwable $e) use ($handler) {
            $statusCode = $handler->getStatusCode($e);
            if ($statusCode === 500) {
                return $handler->renderException($e);
            }
        });
        
    })->create();
