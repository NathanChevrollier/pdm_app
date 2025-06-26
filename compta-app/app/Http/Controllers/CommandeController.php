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
    public function index(Request $request): View
    {
        $query = Commande::with(['user', 'vehicule']);
        
        // Filtre par employé (vendeur)
        if ($request->has('employe_id') && $request->employe_id != '') {
            $query->where('user_id', $request->employe_id);
        }
        
        // Recherche par nom de client
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nom_client', 'like', "%{$search}%");
        }
        
        // Déterminer le type de tri actif (un seul à la fois)
        $sortType = null;
        $sortDirection = 'desc';
        
        if ($request->has('sort_date') && $request->sort_date != '') {
            $sortType = 'date';
            $sortDirection = $request->sort_date;
        } elseif ($request->has('sort_prix') && $request->sort_prix != '') {
            $sortType = 'prix';
            $sortDirection = $request->sort_prix;
        } elseif ($request->has('sort_prix_final') && $request->sort_prix_final != '') {
            $sortType = 'prix_final';
            $sortDirection = $request->sort_prix_final;
        }
        
        // Appliquer le tri en fonction du type sélectionné
        switch ($sortType) {
            case 'date':
                // Utiliser une sous-requête pour trier par date et heure
                // Si l'heure de date_commande est 00:00, utiliser created_at pour l'heure
                $query->orderByRaw("CASE 
                    WHEN TIME(date_commande) = '00:00:00' THEN 
                        CONCAT(DATE(date_commande), ' ', TIME(created_at)) 
                    ELSE 
                        date_commande 
                    END {$sortDirection}");
                break;
                
            case 'prix':
                // Utiliser le prix du véhicule pour le tri
                $query->join('vehicules', 'commandes.vehicule_id', '=', 'vehicules.id')
                      ->select('commandes.*')
                      ->orderBy('vehicules.prix_vente', $sortDirection);
                break;
                
            case 'prix_final':
                $query->orderBy('prix_final', $sortDirection);
                break;
                
            default:
                // Par défaut, tri par date de création décroissante
                $query->latest();
                break;
        }
        
        $commandes = $query->paginate(10)->withQueryString();
        
        // Récupérer la liste des employés pour le filtre
        $employes = User::all();
        
        return view('commandes.index', compact('commandes', 'employes'));
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
        // Utilisateur connecté pour pré-remplir le champ vendeur
        $utilisateurConnecte = auth()->user();
        return view('commandes.create', compact('employes', 'vehicules', 'utilisateurConnecte'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Si l'utilisateur n'est pas admin ou gérant, on force l'ID de l'utilisateur connecté
        if (!auth()->user()->isAdmin() && auth()->user()->statut !== 'gerant' && auth()->user()->statut !== 'co-gerant') {
            $request->merge(['user_id' => auth()->id()]);
        }
        
        $validated = $request->validate([
            'nom_client' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'vehicule_id' => 'required|exists:vehicules,id',
            'reduction_pourcentage' => 'nullable|numeric|min:0|max:100',
            'date_commande' => 'required|date_format:Y-m-d\TH:i',
            'statut' => 'required|string',
        ]);

        // Récupérer le véhicule pour calculer le prix final
        $vehicule = Vehicule::findOrFail($request->vehicule_id);
        // S'assurer que reduction_pourcentage n'est jamais null
        $reduction = $request->reduction_pourcentage !== null ? $request->reduction_pourcentage : 0;
        $validated['reduction_pourcentage'] = $reduction;
        
        // Définir le statut par défaut à "terminée" si non spécifié
        if (!isset($validated['statut']) || empty($validated['statut'])) {
            $validated['statut'] = 'terminée';
        }
        
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
        // Utilisateur connecté pour vérifier les permissions
        $utilisateurConnecte = auth()->user();
        
        // S'assurer que la date de commande est bien définie
        if (empty($commande->date_commande)) {
            $commande->date_commande = $commande->created_at->format('Y-m-d');
        }
        
        return view('commandes.edit', compact('commande', 'employes', 'vehicules', 'utilisateurConnecte'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Commande $commande): RedirectResponse
    {
        // Si l'utilisateur n'est pas admin ou gérant, on force l'ID de l'utilisateur connecté
        if (!auth()->user()->isAdmin() && auth()->user()->statut !== 'gerant' && auth()->user()->statut !== 'co-gerant') {
            $request->merge(['user_id' => $commande->user_id]);
        }
        
        $validated = $request->validate([
            'nom_client' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'vehicule_id' => 'required|exists:vehicules,id',
            'reduction_pourcentage' => 'nullable|numeric|min:0|max:100',
            'date_commande' => 'required|date_format:Y-m-d\TH:i',
            'statut' => 'required|string',
        ]);

        // Récupérer le véhicule pour calculer le prix final
        $vehicule = Vehicule::findOrFail($request->vehicule_id);
        // S'assurer que reduction_pourcentage n'est jamais null
        $reduction = $request->reduction_pourcentage !== null ? $request->reduction_pourcentage : 0;
        $validated['reduction_pourcentage'] = $reduction;
        
        // Définir le statut par défaut à "terminée" si non spécifié
        if (!isset($validated['statut']) || empty($validated['statut'])) {
            $validated['statut'] = 'terminée';
        }
        
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
