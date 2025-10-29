<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Les groupes de middleware de l'application.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // Liste des middlewares pour les requêtes web
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
    protected $middlewareAliases = [
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'jwt.verify' => \App\Http\Middleware\JwtMiddleware::class,
    ];

    /**
     * Les middleware globaux de l'application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'permission' => \App\Http\Middleware\CheckPermission::class,
        'role' => \App\Http\Middleware\CheckRole::class,
    ];

    // Autres méthodes et propriétés...
}