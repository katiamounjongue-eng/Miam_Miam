<?php

// ==========================================
// app/Http/Middleware/CheckPermission.php
// ==========================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuthService;

/**
 * Middleware pour vérifier les permissions
 */
class CheckPermission
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        if (!$this->authService->hasPermission($user, $permission)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas la permission d\'effectuer cette action',
                'required_permission' => $permission
            ], 403);
        }

        return $next($request);
    }
}
