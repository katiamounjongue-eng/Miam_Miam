<?php

namespace App\Http\Controllers;

use App\Models\UserType;
use Illuminate\Http\Request;

class UserTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = UserType::all();
        return view('user_types.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_type_name' => 'required|string|max:255|unique:user_type,user_type_name',
        ]);

        // Génération automatique de l'ID
        $prefix = strtoupper(substr($request->user_type_name, 0, 2));

        // Chercher le dernier ID commençant par ce préfixe
        $last = UserType::where('user_type_id', 'like', $prefix . '%')
                        ->orderBy('user_type_id', 'desc')
                        ->first();

        if ($last) {
            $number = intval(substr($last->user_type_id, 2)) + 1;
        } else {
            $number = 1;
        }

        $user_type_id = $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);

        UserType::create([
            'user_type_id' => $user_type_id,
            'user_type_name' => $request->user_type_name,
        ]);

        return redirect()->route('user_types.index')->with('success', 'Type d’utilisateur ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $type = UserType::findOrFail($id);
        return view('user_types.show', compact('type'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $type = UserType::findOrFail($id);
        return view('user_types.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     * On ne peut modifier que le nom, pas l'ID
     */
    public function update(Request $request, $id)
    {
        $type = UserType::findOrFail($id);

        $request->validate([
            'user_type_name' => 'required|string|max:255|unique:user_type,user_type_name,' . $id . ',user_type_id',
        ]);

        $type->update([
            'user_type_name' => $request->user_type_name,
        ]);

        return redirect()->route('user_types.index')->with('success', 'Type d’utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     * On interdit la suppression
     */
    public function destroy($id)
    {
        return redirect()->route('user_types.index')->with('error', 'Impossible de supprimer un type d’utilisateur.');
    }
}
