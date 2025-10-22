<?php

namespace App\Http\Controllers;

use App\Models\Sponsorship;
use Illuminate\Http\Request;

class SponsorshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sponsorships = Sponsorship::all();
        return response()->json($sponsorships);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sponsorships.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|max:8',
            'godchild_id' => 'nullable|string|max:8',
            'sponsorship_code' => 'nullable|string|max:10|unique:sponsorships,sponsorship_code',
        ]);

        // Génération automatique du sponsorship_id
        $last = Sponsorship::orderBy('sponsorship_relation_id', 'desc')->first();
        $number = $last ? intval(substr($last->sponsorship_relation_id, 2)) + 1 : 1;
        $sponsorship_id = 'SR' . str_pad($number, 6, '0', STR_PAD_LEFT);

        $sponsorship = Sponsorship::create([
            'sponsorship_relation_id' => $sponsorship_id,
            'student_id' => $request->student_id,
            'godchild_id' => $request->godchild_id,
            'sponsorship_code' => $request->sponsorship_code,
        ]);

        return response()->json([
            'message' => 'Parrainage créé avec succès',
            'sponsorship' => $sponsorship
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sponsorship = Sponsorship::findOrFail($id);
        return response()->json($sponsorship);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $sponsorship = Sponsorship::findOrFail($id);
        return view('sponsorships.edit', compact('sponsorship'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $sponsorship = Sponsorship::findOrFail($id);

        $request->validate([
            'student_id' => 'sometimes|string|max:8',
            'godchild_id' => 'nullable|string|max:8',
            'sponsorship_code' => 'nullable|string|max:10|unique:sponsorships,sponsorship_code,' . $id . ',sponsorship_relation_id',
        ]);

        $sponsorship->update($request->all());

        return response()->json([
            'message' => 'Parrainage mis à jour avec succès',
            'sponsorship' => $sponsorship
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sponsorship = Sponsorship::findOrFail($id);
        $sponsorship->delete();

        return response()->json(['message' => 'Parrainage supprimé avec succès']);
    }
}
