<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem; // modèle correspondant
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = OrderItem::all();
        return response()->json($items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des champs de la requête
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'item_id' => 'required|exists:item,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        // Génération automatique du nouvel ID
        $lastItem = OrderItem::orderBy('id', 'desc')->first();
        if ($lastItem) {
            // Récupère la partie numérique et incrémente
            $num = intval(substr($lastItem->id, 2)) + 1;
        } else {
            $num = 1;
        }

        // Recrée l'ID sous le format OI000001
        $newId = 'OI' . str_pad($num, 6, '0', STR_PAD_LEFT);

        // Création du nouvel Order Item
        $orderItem = OrderItem::create([
            'id' => $newId,
            'order_id' => $request->order_id,
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'price' => $request->price,
        ]);

        return response()->json([
            'message' => 'Order item créé avec succès',
            'data' => $orderItem
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orderItem = OrderItem::findOrFail($id);
        return response()->json($orderItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $orderItem = OrderItem::findOrFail($id);

        $request->validate([
            'quantity' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $orderItem->update($request->only(['quantity', 'price']));

        return response()->json([
            'message' => 'Order item mis à jour avec succès',
            'data' => $orderItem
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orderItem = OrderItem::findOrFail($id);
        $orderItem->delete();

        return response()->json(['message' => 'Order item supprimé avec succès']);
    }
}
