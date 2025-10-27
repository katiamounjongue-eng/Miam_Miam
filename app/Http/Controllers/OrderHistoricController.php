<?php

namespace App\Http\Controllers;

use App\Models\OrderHistoric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoricController extends Controller
{
    /**
     * Affiche l'historique des commandes pour l'utilisateur connecté
     */
    public function indexStudent()
    {
        $userId = Auth::id(); // Récupère l'ID de l'utilisateur authentifié

        $historics = OrderHistoric::where('user_id', $userId)
            ->with([
                'order.items',   // Charge les items de la commande
                'order.status'   // Charge le statut de la commande
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Historique de vos commandes récupéré avec succès.',
            'data' => $historics
        ]);
    }

    /**
     * Affiche l'historique de toutes les commandes (admin)
     */
    public function indexAdmin(Request $request)
    {
        $historics = OrderHistoric::with([
                'order.user',    // Charge l'utilisateur
                'order.items',   // Charge les items
                'order.status'   // Charge le statut
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 50)); // Pagination configurable

        return response()->json([
            'message' => 'Historique complet des commandes récupéré avec succès.',
            'data' => $historics
        ]);
    }
}
