<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Resources\OrderResource;
use App\Http\Requests\OrderHistoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

    // Eager loading pour éviter N+1 et fournir les relations nécessaires à la Resource
    $query = Order::with(['user:id,full_name', 'items.menu'])->orderByDesc('created_at');

        if (isset($user->restaurant_id) && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }

        if ($request->has('status')) {
            $statuses = explode(',', $request->input('status'));
            $query->whereIn('status', $statuses);
        }

    $orders = $query->paginate(15);

    // Retourne une collection paginée via la Resource (contrat de données côté frontend)
    return OrderResource::collection($orders);
    }

    /**
     * Mise à jour du statut d'une commande.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:processing,delivered,rejected,completed',
        ]);

    $order->status = $request->input('status');
    $order->save();

    // Retourne la resource mise à jour
    return new OrderResource($order->load('user', 'items.menu'));
    }

    /**
     * Affiche l'historique des commandes, accessible par les équipes du restaurant.
     * Permet la recherche par utilisateur, ID, date et statut.
     */
    public function history(OrderHistoryRequest $request)
    {
        $user = Auth::user();

        // 1. Initialisation de la requête avec les relations nécessaires
        $query = Order::with(['user:id,full_name', 'items.menu']);

        // 2. Filtrage Multi-Tenant
        if (isset($user->restaurant_id) && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }

        // 3. Application des filtres de la maquette (Filter Order)
        // Filtrer par statut (ex: /history?status=completed)
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Recherche par ID/Client (Simule la barre de recherche en haut)
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                // Recherche par ID de commande
                $q->where('id', 'like', "%{$searchTerm}%")
                  // Recherche par nom de client
                  ->orWhereHas('user', function ($uq) use ($searchTerm) {
                      $uq->where('full_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Filtre par période (start_date et end_date)
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        // Tri Custom
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination contrôlée
        $perPage = (int) $request->input('per_page', 30);
        $orders = $query->paginate($perPage);

        return OrderResource::collection($orders);
    }
}


