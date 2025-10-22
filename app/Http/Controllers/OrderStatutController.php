<?php

namespace App\Http\Controllers;

use App\Models\OrderStatut;
use Illuminate\Http\Request;

class OrderStatutController extends Controller
{
    /**
     * Display a listing of all order statuses.
     */
    public function index()
    {
        $statuses = OrderStatut::all();
        return response()->json($statuses);
    }

    /**
     * Show the form for creating a new order status.
     */
    public function create()
    {
        return view('order_statut.create');
    }

    /**
     * Store a newly created order status in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_statut_name' => 'required|string|max:35|unique:order_statut,order_statut_name',
        ]);

        // Génération automatique de l'ID sous la forme OS000001
        $last = OrderStatut::orderBy('order_statut_id', 'desc')->first();
        $number = $last ? intval(substr($last->order_statut_id, 2)) + 1 : 1;
        $order_statut_id = 'OS' . str_pad($number, 6, '0', STR_PAD_LEFT);

        $status = OrderStatut::create([
            'order_statut_id' => $order_statut_id,
            'order_statut_name' => $request->order_statut_name,
        ]);

        return response()->json([
            'message' => 'Statut de commande ajouté avec succès',
            'status' => $status
        ]);
    }

    /**
     * Display the specified order status.
     */
    public function show($id)
    {
        $status = OrderStatut::findOrFail($id);
        return response()->json($status);
    }

    /**
     * Editing is not allowed for predefined statuses.
     */
    public function edit($id)
    {
        abort(403, 'Modification des statuts de commande interdite.');
    }

    /**
     * Updating is not allowed for predefined statuses.
     */
    public function update(Request $request, $id)
    {
        abort(403, 'Modification des statuts de commande interdite.');
    }

    /**
     * Deleting is not allowed for predefined statuses.
     */
    public function destroy($id)
    {
        abort(403, 'Suppression des statuts de commande interdite.');
    }
}
