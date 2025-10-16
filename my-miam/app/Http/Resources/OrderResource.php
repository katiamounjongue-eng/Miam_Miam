<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transforme le modèle Order en un tableau pour la réponse JSON.
     */
    public function toArray(Request $request): array
    {
        // Assure l'existence des relations chargées
        $user = $this->whenLoaded('user');
        $items = $this->whenLoaded('items');

        return [
            // Données de la Commande
            'id' => $this->id,
            'order_number' => '#' . $this->id,
            'date' => $this->created_at ? $this->created_at->format('d M Y, H:i') : null,
            'status' => $this->status,

            // Informations Client
            'client_initial' => $user && ($user->full_name ?? null) ? strtoupper(substr($user->full_name, 0, 1)) : '?',
            'client_name' => $user->full_name ?? 'Utilisateur inconnu',

            // Détails du Coût
            'total_amount' => is_null($this->total_amount) ? null : number_format($this->total_amount, 2) . 'F',

            // Articles Commandés
            'items' => $items ? $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name ?? ($item->menu->name ?? 'Article'),
                    'price' => is_null($item->price) ? null : number_format($item->price, 2),
                    'quantity' => $item->quantity,
                    'sub_total' => is_null($item->price) ? null : number_format($item->price * $item->quantity, 2),
                ];
            }) : [],
        ];
    }
}
