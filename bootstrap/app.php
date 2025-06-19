<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// Importe o middleware do Inertia
use App\Http\Middleware\HandleInertiaRequests; // <-- Correto

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Registre o middleware do Inertia aqui no grupo 'web'
        $middleware->web(append: [
            HandleInertiaRequests::class, // <-- Correto, adiciona o middleware Inertia ao grupo 'web'
        ]);

        // Se você precisar configurar middlewares para a API, faria algo como:
        // $middleware->api(append: [
        //      // Algum middleware específico da API
        // ]);

        // Ou configurar aliases de middleware:
        // $middleware->alias([
        //      // 'alias' => \App\Http\Middleware\SomeMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

