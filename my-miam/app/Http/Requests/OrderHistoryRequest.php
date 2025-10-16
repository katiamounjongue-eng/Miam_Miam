<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderHistoryRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // L'autorisation par rôle/middleware devrait être faite au niveau des routes.
        return true;
    }

    /**
     * Règles de validation pour les filtres d'historique des commandes.
     */
    public function rules(): array
    {
        // Liste des statuts valides (centraliser si possible dans Order::STATUSES)
        $validStatuses = ['pending', 'processing', 'delivered', 'rejected', 'completed', 'TERMINÉ', 'EN ROUTE', 'PRÉPARATION'];

        return [
            'search' => 'nullable|string|max:255',
            // Le paramètre status peut contenir une seule valeur ou plusieurs séparées par des virgules
            'status' => ['nullable', 'string', Rule::in($validStatuses)],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort_by' => ['nullable', 'string', Rule::in(['created_at', 'total_amount'])],
            'sort_order' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }
}
