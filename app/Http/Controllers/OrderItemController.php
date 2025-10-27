<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orderitem; // modèle correspondant
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderitemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Orderitem::all();
        return response()->json($items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des champs de la requête
        $request->validate([
            'order_id' => 'required|exists:orders_id',
            'item_id' => 'required|exists:item_id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        // Génération automatique du nouvel ID
        $lastitem = Orderitem::orderBy('id', 'desc')->first();
        if ($lastitem) {
            // Récupère la partie numérique et incrémente
            $num = intval(substr($lastitem->id, 2)) + 1;
        } else {
            $num = 1;
        }

        // Recrée l'ID sous le format OI000001
        $newId = 'OI' . str_pad($num, 6, '0', STR_PAD_LEFT);

        // Création du nouvel Order item
        $orderitem = Orderitem::create([
            'id' => $newId,
            'order_id' => $request->order_id,
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'price' => $request->price,
        ]);

        return response()->json([
            'message' => 'Order item créé avec succès',
            'data' => $orderitem
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orderitem = Orderitem::findOrFail($id);
        return response()->json($orderitem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $orderitem = Orderitem::findOrFail($id);

        $request->validate([
            'quantity' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $orderitem->update($request->only(['quantity', 'price']));

        return response()->json([
            'message' => 'Order item mis à jour avec succès',
            'data' => $orderitem
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orderitem = Orderitem::findOrFail($id);
        $orderitem->delete();

        return response()->json(['message' => 'Order item supprimé avec succès']);
    }
}
