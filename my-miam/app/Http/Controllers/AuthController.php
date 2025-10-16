<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Point clé : La validation des données reçues du frontend React
    public function register(Request $request)
    {
        // 1. Définition des règles de validation (Critique !)
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|regex:/[A-Z]/|regex:/[0-9]/|confirmed',
            'phone_number' => 'required|string|max:20',
            'localisation' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422); // 422 Unprocessable Entity
        }

        // 2. Création de l'utilisateur
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Le cast 'password' => 'hashed' dans le modèle User prend le relais, mais c'est une bonne pratique de hacher ici.
            'phone_number' => $request->phone_number,
            'localisation' => $request->localisation,
            'role' => 'student', // Par défaut, tout nouvel inscrit est un étudiant
        ]);
        $user->assignRole('student'); // Assigner le rôle via Spatie

        // 3. Génération du Token JWT pour connexion automatique
        $token = auth()->login($user);
        
        return $this->respondWithToken($token, $user);
    }
    
    // Méthode utilitaire pour uniformiser la réponse JSON
    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ], 201); // 201 Created
    }

    public function login(Request $request)
    {
        // 1. Validation des champs (Email et Mot de passe requis)
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            // Note: Le champ "Username" dans la maquette sera géré ici par l'email
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422); // 422 Unprocessable Entity
        }

        // 2. Tenter l'authentification et obtenir le token
        $credentials = $request->only('email', 'password');

        // La fonction `auth()->attempt()` vérifie les identifiants
        // et retourne le JWT si c'est réussi.
        if (! $token = auth()->attempt($credentials)) {
            // Échec de l'authentification (mauvais email ou mot de passe)
            return response()->json(['error' => 'Identifiants non valides'], 401); // 401 Unauthorized
        }

        // 3. Authentification réussie, retourner le token et l'utilisateur
        return $this->respondWithToken($token, auth()->user());
    }

    // NOUVELLE MÉTHODE : Pour se déconnecter (invalider le token)
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Déconnexion réussie']);
    }
    
}
