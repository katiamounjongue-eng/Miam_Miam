<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Contrôleur d'authentification
 */
class AuthController extends Controller
{
    protected $authService;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'refresh']]);
    }


    public function login(Request $request)
    {
        
        try {
            $request->validate([
                'identifier' => 'required|string',
                'password' => 'required|string'
            ]);

            $result = $this->authService->login($request->only(['identifier', 'password']));

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Déconnexion
     * 
     * POST /api/auth/logout
     * Headers: Authorization: Bearer {token}
     */
    public function logout(Request $request)
    {
        try {
            $result = $this->authService->logout($request->user());

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Déconnexion de tous les appareils
     * 
     * POST /api/auth/logout-all
     * Headers: Authorization: Bearer {token}
     */
    public function logoutAll(Request $request)
    {
        try {
            $result = $this->authService->logoutAll($request->user());

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rafraîchir le token
     * 
     * POST /api/auth/refresh
     * Headers: Authorization: Bearer {token}
     */
    public function refresh(Request $request)
    {
        try {
            $result = $this->authService->refreshToken($request->user());

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les informations de l'utilisateur connecté
     * 
     * GET /api/auth/me
     * Headers: Authorization: Bearer {token}
     */
    public function me(Request $request)
    {
        try {
            $result = $this->authService->me($request->user());

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Changer le mot de passe
     * 
     * POST /api/auth/change-password
     * Headers: Authorization: Bearer {token}
     * Body: {
     *   "current_password": "OldP@ss123",
     *   "new_password": "NewP@ss123",
     *   "new_password_confirmation": "NewP@ss123"
     * }
     */
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:255',
                    'confirmed',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                    Rule::unique('users', 'password'),
                ],
            ], [
                'new_password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un symbole.'
            ]);

            $result = $this->authService->changePassword(
                $request->user(),
                $request->only(['current_password', 'new_password'])
            );

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Inscription d'un nouveau client
     * 
     * POST /api/auth/register
     * Body: {
     *   "first_name": "Jean",
     *   "last_name": "Dupont",
     *   "password": "SecureP@ss123",
     *   "password_confirmation": "SecureP@ss123",
     *   "mail_adress": "jean@example.com",
     *   "phone_number": "237612345678"
     * }
     */
    public function register(RegisterRequest  $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:255',
                    'confirmed',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                    Rule::unique('users', 'password'),
                ],
                'mail_adress' => 'nullable|email|unique:users,mail_adress',
                'phone_number' => 'nullable|digits:12|unique:users,phone_number',
            ], [
                'password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un symbole.'
            ]);

            if (!$request->mail_adress && !$request->phone_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez fournir soit une adresse mail, soit un numéro de téléphone'
                ], 422);
            }

            // Créer le compte client
            $userController = new UsersController();
            $userRequest = new Request([
                'user_type_id' => \App\Models\UserType::where('user_type_name', 'Client')->first()->user_type_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'password' => $request->password,
                'mail_adress' => $request->mail_adress,
                'phone_number' => $request->phone_number,
                'account_statut' => true,
            ]);

            $response = $userController->store($userRequest);
            $userData = json_decode($response->getContent(), true);

            if (!$userData['success']) {
                throw new \Exception($userData['message']);
            }

            // Connecter automatiquement l'utilisateur
            $loginResult = $this->authService->login([
                'identifier' => $request->mail_adress ?? $request->phone_number,
                'password' => $request->password
            ]);

            DB::commit();

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie',
                'user' => $loginResult['user'],
                'token' => $loginResult['token'],
                'token_type' => 'Bearer'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir toutes les permissions disponibles
     * 
     * GET /api/auth/permissions
     * Headers: Authorization: Bearer {token}
     */
    public function getPermissions(Request $request)
    {
        try {
            $permissions = $this->authService->getAllPermissions();

            return response()->json([
                'success' => true,
                'permissions' => $permissions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}