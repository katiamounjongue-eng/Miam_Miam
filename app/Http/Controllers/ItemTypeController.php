<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class itemTypeController extends Controller
{
    /**
     * Affiche la liste de tous les types d'articles.
     */
    public function index()
    {
        $itemTypes = itemType::all();

        return response()->json([
            'message' => 'Liste des types d\'articles récupérée avec succès.',
            'data' => $itemTypes
        ]);
    }

    /**
     * Crée un nouveau type d'article.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_type_name' => 'required|string|max:255|unique:item_type,item_type_name',
        ]);

        $itemType = itemType::create($request->only('item_type_name'));

        return response()->json([
            'message' => 'Type d\'article créé avec succès.',
            'data' => $itemType
        ], 201);
    }

    /**
     * Affiche un type d'article spécifique.
     */
    public function show($id)
    {
        $itemType = itemType::findOrFail($id);

        return response()->json([
            'message' => 'Type d\'article récupéré avec succès.',
            'data' => $itemType
        ]);
    }

    /**
     * Met à jour un type d'article.
     */
    public function update(Request $request, $id)
    {
        $itemType = itemType::findOrFail($id);

        $request->validate([
            'item_type_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('item_type', 'item_type_name')->ignore($itemType->item_type_id, 'item_type_id'),
            ],
        ]);

        $itemType->update($request->only('item_type_name'));

        return response()->json([
            'message' => 'Type d\'article mis à jour avec succès.',
            'data' => $itemType
        ]);
    }

    /**
     * Supprime un type d'article, si aucun article n'est lié.
     */
    public function destroy($id)
    {
        $itemType = itemType::findOrFail($id);

        if ($itemType->items()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer : des articles sont liés à ce type.'
            ], 409);
        }

        $itemType->delete();

        return response()->json([
            'message' => 'Type d\'article supprimé avec succès.'
        ], 204);
    }
}
