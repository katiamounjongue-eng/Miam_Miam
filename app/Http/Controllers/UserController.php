<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    /**
     * Display all users
     */
    public function index()
    {
        $users = Users::all();
        return response()->json($users);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_type_id' => 'required|string|max:8',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'user_password' => [
                'required',
                'string',
                'max:15',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/', // min 1 maj, 1 min, 1 chiffre, 1 symbole
                Rule::unique('users', 'user_password'),
            ],
            'mail_adress' => 'nullable|email|unique:users,mail_adress',
            'phone_number' => 'nullable|digits:12|unique:users,phone_number',
            'inscription_date' => 'required|date',
            'account_statut' => 'required|boolean',
        ], [
            'user_password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un symbole.'
        ]);

        if (!$request->mail_adress && !$request->phone_number) {
            return response()->json(['error' => 'Vous devez fournir soit une adresse mail, soit un numéro de téléphone.'], 422);
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
            'user_password' => bcrypt($request->user_password),
            'mail_adress' => $request->mail_adress,
            'phone_number' => $request->phone_number,
            'inscription_date' => $request->inscription_date,
            'account_statut' => $request->account_statut,
        ]);

        return response()->json(['message' => 'Utilisateur créé avec succès', 'user' => $user]);
    }

    /**
     * Display a specific user
     */
    public function show($id)
    {
        $user = Users::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update a user
     */
    public function update(Request $request, $id)
    {
        $user = Users::findOrFail($id);

        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'user_password' => [
                'sometimes',
                'string',
                'max:15',
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
            return response()->json(['error' => "Vous ne pouvez pas modifier le type d'utilisateur."], 403);
        }

        $data = $request->all();
        if (isset($data['user_password'])) {
            $data['user_password'] = bcrypt($data['user_password']);
        }

        $user->update($data);

        return response()->json(['message' => 'Utilisateur mis à jour avec succès', 'user' => $user]);
    }

    /**
     * Delete a user
     */
    public function destroy($id)
    {
        $user = Users::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }
}
