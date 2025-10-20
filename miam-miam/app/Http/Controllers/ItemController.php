<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    // Liste des articles
    public function index()
    {
        // Inclure le type d'article pour l'affichage 
        $items = Item::with('type')->get();
        return response()->json($items);
    }

    // CREATE 
    public function store(Request $request)
    {
        $request->validate([
            'item_type_id' => 'required|exists:Item_type,item_type_id',
            'name' => 'required|string|max:255|unique:Item,name',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0.01',
            'image_link' => 'required|url|max:255',
        ]);

        $item = Item::create($request->all());

        return response()->json([
            'message' => 'Article de menu créé avec succès.',
            'data' => $item
        ], 201);
    }

    // READ ONE
    public function show($id)
    {
        $item = Item::with('type')->findOrFail($id);
        return response()->json($item);
    }

    // UPDATE 
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        
        $request->validate([
            'item_type_id' => 'sometimes|exists:Item_type,item_type_id',
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('Item')->ignore($item->item_id, 'item_id')],
            'description' => 'sometimes|required|string|max:255',
            'quantity' => 'sometimes|required|integer|min:0',
            'price' => 'sometimes|required|numeric|min:0.01',
            'image_link' => 'sometimes|required|url|max:255',
        ]);

        $item->update($request->all());

        return response()->json([
            'message' => 'Article de menu mis à jour avec succès.',
            'data' => $item
        ]);
    }

    // DELETE 
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        

        if ($item->orderItems()->exists()) {
             return response()->json(['message' => 'Impossible de supprimer : cet article a été commandé. Marquez-le comme épuisé.'], 409);
        }

        $item->delete();

        return response()->json(['message' => 'Article supprimé avec succès.'], 204);
    }
}