<?php

namespace App\Http\Controllers;

use App\Models\Localisation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocalisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $localisations = Localisation::all();
        return view('localisations.index', compact('localisations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('localisations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'localisation_name' => 'required|string|max:255|unique:localisation,localisation_name',
            'localisation_delevery_price' => 'required|numeric|min:0',
        ]);

        // Génération automatique de l'ID sous la forme LC00001, LC00002, ...
        $last = Localisation::orderBy('localisation_id', 'desc')->first();
        if ($last) {
            $number = intval(substr($last->localisation_id, 2)) + 1;
        } else {
            $number = 1;
        }
        $localisation_id = 'LC' . str_pad($number, 5, '0', STR_PAD_LEFT);

        Localisation::create([
            'localisation_id' => $localisation_id,
            'localisation_name' => $request->localisation_name,
            'localisation_delevery_price' => $request->localisation_delevery_price,
        ]);

        return redirect()->route('localisations.index')->with('success', 'Localisation ajoutée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $localisation = Localisation::findOrFail($id);
        return view('localisations.show', compact('localisation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $localisation = Localisation::findOrFail($id);
        return view('localisations.edit', compact('localisation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $localisation = Localisation::findOrFail($id);

        $request->validate([
            'localisation_name' => 'required|string|max:255|unique:localisation,localisation_name,' . $id . ',localisation_id',
            'localisation_delevery_price' => 'required|numeric|min:0',
        ]);

        $localisation->update([
            'localisation_name' => $request->localisation_name,
            'localisation_delevery_price' => $request->localisation_delevery_price,
        ]);

        return redirect()->route('localisations.index')->with('success', 'Localisation mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $localisation = Localisation::findOrFail($id);
        $localisation->delete();

        return redirect()->route('localisations.index')->with('success', 'Localisation supprimée avec succès.');
    }
}
