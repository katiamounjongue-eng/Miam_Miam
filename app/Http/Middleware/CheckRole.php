<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuthService;

/**
 * Middleware pour vérifier les rôles
 */
class CheckRole
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        if (!$this->authService->hasRole($user, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Rôle insuffisant.',
                'required_roles' => $roles,
                'your_role' => $user->userType->user_type_name ?? 'Unknown'
            ], 403);
        }

        return $next($request);
    }
}