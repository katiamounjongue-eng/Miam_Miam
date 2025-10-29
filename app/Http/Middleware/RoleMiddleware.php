<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }
        $user = auth()->user();

        if (!$user->hasRole($roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé. Rôle requis: ' . implode(', ', $roles)
            ], 403);
        }

        return $next($request);
    }
}