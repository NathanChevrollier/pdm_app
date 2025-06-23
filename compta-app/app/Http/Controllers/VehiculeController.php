<?php

namespace App\Http\Controllers;

use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VehiculeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $vehicules = Vehicule::latest()->paginate(10);
        return view('vehicules.index', compact('vehicules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('vehicules.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation mise à jour pour correspondre aux champs réels du modèle Vehicule
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
        ]);
        
        // Ajouter le statut par défaut 'disponible' puisqu'il n'est pas dans le formulaire
        $validated['statut'] = 'disponible';

        Vehicule::create($validated);

        return redirect()->route('vehicules.index')
                        ->with('success', 'Véhicule créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicule $vehicule): View
    {
        return view('vehicules.show', compact('vehicule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicule $vehicule): View
    {
        return view('vehicules.edit', compact('vehicule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicule $vehicule): RedirectResponse
    {
        // Validation mise à jour pour correspondre aux champs réels du modèle Vehicule
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
        ]);
        
        // Conserver le statut existant du véhicule puisqu'il n'est pas dans le formulaire
        $validated['statut'] = $vehicule->statut;

        $vehicule->update($validated);

        return redirect()->route('vehicules.index')
                        ->with('success', 'Véhicule mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicule $vehicule): RedirectResponse
    {
        $vehicule->delete();

        return redirect()->route('vehicules.index')
                        ->with('success', 'Véhicule supprimé avec succès');
    }
}
