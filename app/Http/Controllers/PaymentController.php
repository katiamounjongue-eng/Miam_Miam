<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Affiche tous les moyens de paiement
     */
    public function index()
    {
        $payments = Payment::all();
        return response()->json($payments);
    }

    /**
     * Crée un nouveau moyen de paiement
     */
    public function store(Request $request)
    {
        $request->validate([
            'method_name' => 'required|string|max:50|unique:payment,method_name',
            'description' => 'nullable|string|max:255',
        ]);

        $lastPayment = Payment::orderBy('payment_method_id', 'desc')->first();
        $num = $lastPayment ? intval(substr($lastPayment->payment_method_id, 2)) + 1 : 1;
        $newId = 'PM' . str_pad($num, 6, '0', STR_PAD_LEFT);

        $payment = Payment::create([
            'payment_method_id' => $newId,
            'method_name' => $request->method_name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Moyen de paiement créé avec succès',
            'data' => $payment
        ], 201);
    }

    /**
     * Affiche un moyen de paiement spécifique
     */
    public function show($id)
    {
        $payment = Payment::findOrFail($id);
        return response()->json($payment);
    }

    /**
     * Met à jour un moyen de paiement
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'payment_name' => 'sometimes|required|string|max:50|unique:payments,payment_name,' . $id . ',payment_id',
            'description' => 'nullable|string|max:255',
        ]);

        $payment->update($request->only(['payment_name', 'description']));

        return response()->json([
            'message' => 'Moyen de paiement mis à jour avec succès',
            'data' => $payment
        ]);
    }

    /**
     * Supprime un moyen de paiement
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json(['message' => 'Moyen de paiement supprimé avec succès']);
    }
}
