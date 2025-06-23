<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class CommandeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $commandes = Commande::with(['user', 'vehicule'])->latest()->paginate(10);
        return view('commandes.index', compact('commandes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Récupérer tous les utilisateurs sans distinction de statut
        $employes = User::all();
        // Récupérer tous les véhicules pour la création de commande
        $vehicules = Vehicule::all();
        return view('commandes.create', compact('employes', 'vehicules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom_client' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'vehicule_id' => 'required|exists:vehicules,id',
            'reduction_pourcentage' => 'nullable|numeric|min:0|max:100',
            'date_commande' => 'required|date',
        ]);

        // Récupérer le véhicule pour calculer le prix final
        $vehicule = Vehicule::findOrFail($request->vehicule_id);
        $reduction = $request->reduction_pourcentage ?? 0;
        
        // Calculer le prix final avec la réduction
        $validated['prix_final'] = Commande::calculerPrixFinal(
            $vehicule->prix_vente, 
            $reduction
        );

        Commande::create($validated);

        return redirect()->route('commandes.index')
                        ->with('success', 'Commande créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Commande $commande): View
    {
        $commande->load(['user', 'vehicule']);
        return view('commandes.show', compact('commande'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commande $commande): View
    {
        // Récupérer tous les utilisateurs sans distinction de statut
        $employes = User::all();
        // Récupérer tous les véhicules pour l'édition de commande
        $vehicules = Vehicule::all();
        return view('commandes.edit', compact('commande', 'employes', 'vehicules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Commande $commande): RedirectResponse
    {
        $validated = $request->validate([
            'nom_client' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'vehicule_id' => 'required|exists:vehicules,id',
            'reduction_pourcentage' => 'nullable|numeric|min:0|max:100',
            'date_commande' => 'required|date',
        ]);

        // Récupérer le véhicule pour calculer le prix final
        $vehicule = Vehicule::findOrFail($request->vehicule_id);
        $reduction = $request->reduction_pourcentage ?? 0;
        
        // Calculer le prix final avec la réduction
        $validated['prix_final'] = Commande::calculerPrixFinal(
            $vehicule->prix_vente, 
            $reduction
        );

        $commande->update($validated);

        return redirect()->route('commandes.index')
                        ->with('success', 'Commande mise à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Commande $commande): RedirectResponse
    {
        $commande->delete();

        return redirect()->route('commandes.index')
                        ->with('success', 'Commande supprimée avec succès');
    }

    /**
     * Exporte la liste des commandes au format CSV
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="commandes_' . date('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $commandes = Commande::with(['user', 'vehicule'])->get();

        $callback = function() use ($commandes) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, [
                'ID',
                'Client',
                'Employé',
                'Véhicule',
                'Date de commande',
                'Date de création',
                'Dernière mise à jour'
            ]);
            
            // Données
            foreach ($commandes as $commande) {
                fputcsv($file, [
                    $commande->id,
                    $commande->nom_client,
                    $commande->user->getNomComplet() ?? 'N/A',
                    $commande->vehicule->nom ?? 'N/A', // Utilisation du nom au lieu de l'immatriculation
                    $commande->date_commande->format('d/m/Y'),
                    $commande->created_at->format('d/m/Y H:i'),
                    $commande->updated_at->format('d/m/Y H:i')
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
