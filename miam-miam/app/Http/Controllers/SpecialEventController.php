<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecialEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpecialEventController extends Controller
{

    public function index()
    {
        
        $events = SpecialEvent::with('type')->orderBy('event_starting_date', 'desc')->get();
        return response()->json($events);
    }

    
    // Crée un nouvel événement spécial. (CREATE)
     
    public function store(Request $request)
    {
        
        $request->validate([
            'event_type_id' => 'required|exists:Event_Type,event_type_id',
            'event_name' => 'required|string|max:255|unique:Special_Event,event_name',
            'event_starting_date' => 'required|date|after_or_equal:today',
            'event_ending_date' => 'required|date|after_or_equal:event_starting_date',
            'event_description' => 'nullable|string|max:255|unique:Special_Event,event_description',
        ]);

        $event = SpecialEvent::create($request->all());

        return response()->json([
            'message' => 'Événement spécial créé avec succès.',
            'data' => $event
        ], 201);
    }

    
    //Affiche un événement spécifique.
     
    public function show($id)
    {
        $event = SpecialEvent::with('type')->findOrFail($id);
        return response()->json($event);
    }

    
    //Met à jour un événement existant. 
    
    public function update(Request $request, $id)
    {
        $event = SpecialEvent::findOrFail($id);

        
        $request->validate([
            'event_type_id' => 'sometimes|exists:Event_Type,event_type_id',
            'event_name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('Special_Event')->ignore($event->event_id, 'event_id')],
            'event_starting_date' => 'sometimes|required|date|after_or_equal:today',
            'event_ending_date' => 'sometimes|required|date|after_or_equal:event_starting_date',
            'event_description' => ['nullable', 'string', 'max:255', Rule::unique('Special_Event')->ignore($event->event_id, 'event_id')],
        ]);

        $event->update($request->all());

        return response()->json([
            'message' => 'Événement spécial mis à jour avec succès.',
            'data' => $event
        ]);
    }

    
    // Supprime un événement spécial.
     
    public function destroy($id)
    {
        $event = SpecialEvent::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Événement spécial supprimé avec succès.'], 204);
    }
}