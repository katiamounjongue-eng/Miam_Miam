<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller;
use App\Models\ItemType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemTypeController extends Controller
{
    // Affiche la liste des types d'articles 
    public function index()
    {
        $itemTypes = ItemType::all();
        return response()->json($itemTypes);
    }

    // Crée un nouveau type d'article 
    public function store(Request $request)
    {
        //  Validation des données
        $request->validate([
            'item_type_name' => 'required|string|max:255|unique:Item_type',
        ]);

        //  Création de l'entité 
        $itemType = ItemType::create($request->all());

        return response()->json([
            'message' => 'Type d\'article créé avec succès.',
            'data' => $itemType
        ], 201); 
    }

    // Affiche un type d'article spécifique 
    public function show($id)
    {
        $itemType = ItemType::findOrFail($id);
        return response()->json($itemType);
    }

    // Met à jour un type d'article 
    public function update(Request $request, $id)
    {
        $itemType = ItemType::findOrFail($id);

        //  Validation 
        $request->validate([
            'item_type_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('Item_type')->ignore($itemType->item_type_id, 'item_type_id'),
            ],
        ]);

        //  Mise à jour
        $itemType->update($request->all());

        return response()->json([
            'message' => 'Type d\'article mis à jour avec succès.',
            'data' => $itemType
        ]);
    }

    // Supprime un type d'article (
    public function destroy($id)
    {
        $itemType = ItemType::findOrFail($id);
        
        // Vérification de la dépendance avant suppression 
        if ($itemType->items()->exists()) {
            return response()->json(['message' => 'Impossible de supprimer : des articles sont liés à ce type.'], 409); 
        }
        
        $itemType->delete();

        return response()->json(['message' => 'Type d\'article supprimé avec succès.'], 204); 
    }
}