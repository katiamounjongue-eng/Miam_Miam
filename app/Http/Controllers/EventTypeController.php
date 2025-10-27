<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventTypeController extends Controller
{
    /**
     * Affiche la liste de tous les types d'événements.
     */
    public function index()
    {
        $eventTypes = EventType::all();

        return response()->json([
            'message' => 'Liste des types d\'événements récupérée avec succès.',
            'data' => $eventTypes
        ]);
    }

    /**
     * Crée un nouveau type d'événement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_type_name' => 'required|string|max:255|unique:event_type,event_type_name',
        ]);

        $eventType = EventType::create($request->only(['event_type_name']));

        return response()->json([
            'message' => 'Type d\'événement créé avec succès.',
            'data' => $eventType
        ], 201);
    }

    /**
     * Affiche un type d'événement spécifique.
     */
    public function show($id)
    {
        $eventType = EventType::findOrFail($id);

        return response()->json([
            'message' => 'Type d\'événement récupéré avec succès.',
            'data' => $eventType
        ]);
    }

    /**
     * Met à jour un type d'événement existant.
     */
    public function update(Request $request, $id)
    {
        $eventType = EventType::findOrFail($id);

        $request->validate([
            'event_type_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('event_type')->ignore($eventType->event_type_id, 'event_type_id'),
            ],
        ]);

        $eventType->update($request->only(['event_type_name']));

        return response()->json([
            'message' => 'Type d\'événement mis à jour avec succès.',
            'data' => $eventType
        ]);
    }

    /**
     * Supprime un type d'événement.
     */
    public function destroy($id)
    {
        $eventType = EventType::findOrFail($id);

        // Vérification de dépendance : un type ne peut pas être supprimé si des événements sont liés
        if ($eventType->specialEvents()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer : des événements sont liés à ce type.'
            ], 409);
        }

        $eventType->delete();

        return response()->json([
            'message' => 'Type d\'événement supprimé avec succès.'
        ], 204);
    }
}
