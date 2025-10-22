<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Localisation;
use App\Models\OrderStatut;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController  extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user', 'localisation', 'orderStatut'])->get();
        return response()->json($orders);
        }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des champs de la requête
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'localisation_id' => 'required|exists:localisation,localisation_id',
            'order_statut_id' => 'required|exists:order_statut,order_statut_id',
            'order_date' => 'required|date',
        ]);

        // Automatic generation od ID
        $lastOrder = Order::orderBy('order_id', 'desc')->first();
        $num = $lastOrder ? intval(substr($lastOrder->order_id, 2)) + 1 : 1;
        $newId = 'OR' . str_pad($num, 6, '0', STR_PAD_LEFT);

        $order = Order::create([
            'order_id' => $newId,
            'user_id' => $request->user_id,
            'localisation_id' => $request->localisation_id,
            'order_statut_id' => $request->order_statut_id,
            'order_date' => $request->order_date,
        ]);

        // Recrée l'ID sous le format OI000001
        $newId = 'OI' . str_pad($num, 6, '0', STR_PAD_LEFT);

        // Création du nouvel Order Item
        $order = Order::create([
            'order_item_id' => $newId,
            'order_id' => $request->order_id,
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'price' => $request->price,
        ]);

        return response()->json([
            'message' => 'Order créé avec succès',
            'data' => $order
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['user', 'localisation', 'orderStatut'])->findOrFail($id);
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'user_id' => 'sometimes|exists:users,user_id',
            'localisation_id' => 'sometimes|exists:localisation,localisation_id',
            'order_statut_id' => 'sometimes|exists:order_statut,order_statut_id',
            'order_date' => 'sometimes|date',
        ]);

        $order->update($request->only([
            'user_id', 'localisation_id', 'order_statut_id', 'order_date'
        ]));

        return response()->json([
            'message' => 'Commande mise à jour avec succès',
            'data' => $order
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Commande supprimée avec succès']);
    }
}
