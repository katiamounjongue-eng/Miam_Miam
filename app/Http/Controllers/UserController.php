<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    // ==========================================
    // GESTION GÉNÉRALE DES UTILISATEURS
    // ==========================================

    /**
     * Afficher tous les utilisateurs
     * GET /api/users?user_type=Client&status=1&search=John
     */
    public function index(Request $request)
    {
        try {
            $query = Users::with('userType');

            // Filtre par type d'utilisateur
            if ($request->filled('user_type_id')) {
                $query->where('user_type_id', $request->user_type_id);
            }

            // Filtre par nom de type (Client, Admin, etc.)
            if ($request->filled('user_type')) {
                $query->whereHas('userType', function($q) use ($request) {
                    $q->where('user_type_name', $request->user_type);
                });
            }

            // Filtre par statut
            if ($request->has('account_statut')) {
                $query->where('account_statut', $request->account_statut);
            }

            // Recherche
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('mail_adress', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%")
                      ->orWhere('user_id', 'like', "%{$search}%");
                });
            }

            $users = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un nouvel utilisateur
     * POST /api/users
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_type_id' => 'required|string|max:8|exists:User_Type,user_type_id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'user_password' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                    Rule::unique('users', 'user_password'),
                ],
                'mail_adress' => 'nullable|email|unique:users,mail_adress',
                'phone_number' => 'nullable|digits:12|unique:users,phone_number',
                'account_statut' => 'nullable|boolean',
            ], [
                'user_password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un symbole.'
            ]);

            if (!$request->mail_adress && !$request->phone_number) {
                return response()->json([
                    'error' => 'Vous devez fournir soit une adresse mail, soit un numéro de téléphone.'
                ], 422);
            }

            // Générer l'user_id : US_<type_index>_000X
            $type_index = strtoupper(substr($request->user_type_id, -1)); 
            $lastUser = Users::where('user_type_id', $request->user_type_id)
                            ->orderBy('created_at', 'desc')
                            ->first();
            $counter = $lastUser ? ((int)substr($lastUser->user_id, -4)) + 1 : 1;
            $user_id = 'US_' . $type_index . '_' . str_pad($counter, 4, '0', STR_PAD_LEFT);

            $user = Users::create([
                'user_id' => $user_id,
                'user_type_id' => $request->user_type_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'user_password' => Hash::make($request->user_password),
                'mail_adress' => $request->mail_adress,
                'phone_number' => $request->phone_number,
                'inscription_date' => now(),
                'account_statut' => $request->account_statut ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'user' => $user->load('userType')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'utilisateur',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Afficher un utilisateur spécifique
     * GET /api/users/{id}
     */
    public function show($id)
    {
        try {
            $user = Users::with('userType')->findOrFail($id);

            // Statistiques basiques
            $ordersCount = DB::table('orders')->where('user_id', $id)->count();
            $lastOrder = DB::table('orders')->where('user_id', $id)->max('order_date');

            return response()->json([
                'success' => true,
                'user' => $user,
                'statistics' => [
                    'total_orders' => $ordersCount,
                    'last_order_date' => $lastOrder
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur introuvable',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mettre à jour un utilisateur
     * PUT /api/users/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Users::findOrFail($id);

            $request->validate([
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'user_password' => [
                    'sometimes',
                    'string',
                    'max:255',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                    Rule::unique('users', 'user_password')->ignore($id, 'user_id'),
                ],
                'mail_adress' => ['nullable', 'email', Rule::unique('users', 'mail_adress')->ignore($id, 'user_id')],
                'phone_number' => ['nullable', 'digits:12', Rule::unique('users', 'phone_number')->ignore($id, 'user_id')],
                'account_statut' => 'sometimes|boolean',
            ], [
                'user_password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un symbole.'
            ]);

            if ($request->has('user_type_id') && $request->user_type_id != $user->user_type_id) {
                return response()->json([
                    'error' => "Vous ne pouvez pas modifier le type d'utilisateur directement. Utilisez l'endpoint /change-role"
                ], 403);
            }

            $data = $request->only(['first_name', 'last_name', 'mail_adress', 'phone_number', 'account_statut']);
            
            if ($request->filled('user_password')) {
                $data['user_password'] = Hash::make($request->user_password);
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès',
                'user' => $user->fresh('userType')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Supprimer un utilisateur
     * DELETE /api/users/{id}
     */
    public function destroy($id)
    {
        try {
            $user = Users::findOrFail($id);

            // Vérifier les commandes en cours
            $activeOrders = DB::table('orders')
                ->where('user_id', $id)
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('order_statut')
                          ->whereColumn('orders.order_statut_id', 'order_statut.order_statut_id')
                          ->whereIn('order_statut.order_statut_name', ['En attente', 'En préparation', 'En livraison']);
                })
                ->count();

            if ($activeOrders > 0) {
                return response()->json([
                    'error' => 'Impossible de supprimer cet utilisateur car il a des commandes en cours'
                ], 400);
            }

            // Désactiver au lieu de supprimer
            $user->update(['account_statut' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur désactivé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    // ==========================================
    // GESTION SPÉCIFIQUE DES EMPLOYÉS
    // ==========================================

    /**
     * Lister uniquement les employés
     * GET /api/users/employees?role=Admin&status=1
     */
    public function getEmployees(Request $request)
    {
        try {
            $query = Users::with('userType')
                ->whereHas('userType', function($q) {
                    $q->whereIn('user_type_name', ['Admin', 'Manager', 'Chef', 'Serveur', 'Livreur', 'Caissier']);
                });

            // Filtre par rôle spécifique
            if ($request->filled('role')) {
                $query->whereHas('userType', function($q) use ($request) {
                    $q->where('user_type_name', $request->role);
                });
            }

            // Filtre par statut
            if ($request->has('account_statut')) {
                $query->where('account_statut', $request->account_statut);
            }

            // Recherche
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('mail_adress', 'like', "%{$search}%");
                });
            }

            $employees = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'total_employees' => $employees->count(),
                'employees' => $employees
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des employés',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lister uniquement les clients
     * GET /api/users/clients
     */
    public function getClients(Request $request)
    {
        try {
            $query = Users::with('userType')
                ->whereHas('userType', function($q) {
                    $q->where('user_type_name', 'Client');
                });

            if ($request->has('account_statut')) {
                $query->where('account_statut', $request->account_statut);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('mail_adress', 'like', "%{$search}%");
                });
            }

            $clients = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $clients
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des clients',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activer/Désactiver un utilisateur
     * PATCH /api/users/{id}/toggle-status
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|boolean'
            ]);

            $user = Users::findOrFail($id);
            $user->update(['account_statut' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => $request->status ? 'Utilisateur activé' : 'Utilisateur désactivé',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de statut',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Changer le rôle d'un utilisateur
     * PATCH /api/users/{id}/change-role
     */
    public function changeRole(Request $request, $id)
    {
        try {
            $request->validate([
                'new_user_type_id' => 'required|string|exists:User_Type,user_type_id'
            ]);

            $user = Users::findOrFail($id);
            $newUserType = UserType::findOrFail($request->new_user_type_id);
            $oldRole = $user->userType->user_type_name;

            $user->update(['user_type_id' => $request->new_user_type_id]);

            return response()->json([
                'success' => true,
                'message' => "Rôle changé de {$oldRole} à {$newUserType->user_type_name}",
                'user' => $user->fresh('userType')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de rôle',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Statistiques des utilisateurs
     * GET /api/users/statistics
     */
    public function statistics()
    {
        try {
            $stats = UserType::withCount([
                'users as active_count' => function($query) {
                    $query->where('account_statut', true);
                },
                'users as inactive_count' => function($query) {
                    $query->where('account_statut', false);
                }
            ])->get();

            $totalUsers = Users::count();
            $activeUsers = Users::where('account_statut', true)->count();
            $inactiveUsers = Users::where('account_statut', false)->count();

            return response()->json([
                'success' => true,
                'summary' => [
                    'total_users' => $totalUsers,
                    'active_users' => $activeUsers,
                    'inactive_users' => $inactiveUsers
                ],
                'by_type' => $stats->map(function($type) {
                    return [
                        'type_id' => $type->user_type_id,
                        'type_name' => $type->user_type_name,
                        'active_count' => $type->active_count ?? 0,
                        'inactive_count' => $type->inactive_count ?? 0,
                        'total_count' => ($type->active_count ?? 0) + ($type->inactive_count ?? 0)
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}