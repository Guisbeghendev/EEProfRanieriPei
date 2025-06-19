<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\CheckPermission; // Importação necessária para seu middleware personalizado

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registra os middlewares para o grupo 'web'.
        // Esses middlewares são executados em todas as requisições web.
        $middleware->web(append: [
            // Middleware essencial para proteção contra ataques CSRF (Cross-Site Request Forgery).
            // Ele verifica o token CSRF em formulários e requisições POST/PUT/DELETE.
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,

            // Middleware do Inertia.js, responsável por lidar com as requisições do Inertia
            // e renderizar as páginas Vue.js.
            HandleInertiaRequests::class,

            // Middleware para otimização de performance. Adiciona cabeçalhos Link (preload/prefetch)
            // para recursos que o navegador deve pré-carregar, melhorando a velocidade percebida.
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Registra seus aliases de middleware aqui.
        // Aliases permitem que você use nomes curtos e legíveis para seus middlewares
        // nas definições de rota (ex: Route::middleware('check.permission')->get(...)).
        $middleware->alias([
            // Seu middleware personalizado 'CheckPermission'.
            // Ele será invocado quando você usar 'check.permission' nas suas rotas.
            'check.permission' => CheckPermission::class,
        ]);

        // Se você tivesse middlewares para API, eles seriam configurados assim:
        // $middleware->api(append: [
        //     // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        //     // 'throttle:api',
        //     // \Illuminate\Routing\Middleware\SubstituteBindings::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Esta seção é para configurar como seu aplicativo lida com exceções.
        // Você pode registrar relatórios de exceções ou renderizadores personalizados aqui.
    })->create();
