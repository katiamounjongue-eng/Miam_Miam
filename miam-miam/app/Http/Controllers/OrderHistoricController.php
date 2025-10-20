<?php

namespace App\Http\Controllers;

use App\Models\OrderHistoric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoricController extends Controller
{
    // Affiche l'historique pour l'utilisateur connecté 
    public function indexStudent()
    {
        $userId = Auth::id(); // Récupère l'ID de l'utilisateur authentifié
        
        // Récupère l'historique, chargeant l'objet Order complet
        $historics = OrderHistoric::where('user_id', $userId)
                                  ->with(['order.items', 'order.status']) 
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        return response()->json($historics);
    }

    // Affiche l'historique de toutes les commandes 
    public function indexAdmin()
    {
        
        $historics = OrderHistoric::with(['order.user', 'order.status'])
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(50); // Pagination pour performance
        
        return response()->json($historics);
    }

    
}