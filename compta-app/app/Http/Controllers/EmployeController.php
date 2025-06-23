<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Commande;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EmployeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::whereNotNull('statut')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'statut' => 'required|string|in:Patron,Co-patron,Manager,Vendeur,Recrue,Comptable,RH',
            'commission' => 'nullable|numeric|min:0|max:100',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Créer l'utilisateur avec les données de l'employé
        $user = new \App\Models\User();
        $user->name = $validated['nom'];
        $user->prenom = $validated['prenom'] ?? null;
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->statut = $validated['statut'];
        $user->commission = $validated['commission'] ?? 0;
        $user->save();

        return redirect()->route('users.index')
                        ->with('success', 'Employé créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $employe): View
    {
        return view('users.show', compact('employe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $employe): View
    {
        return view('users.edit', compact('employe'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $employe): RedirectResponse
    {
        $rules = [
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employe->id,
            'statut' => 'required|string|in:Patron,Co-patron,Manager,Vendeur,Recrue,Comptable,RH',
            'commission' => 'nullable|numeric|min:0|max:100',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validated = $request->validate($rules);
        
        // Mettre à jour l'utilisateur
        $employe->name = $validated['nom'];
        $employe->prenom = $validated['prenom'] ?? null;
        $employe->email = $validated['email'];
        $employe->statut = $validated['statut'];
        $employe->commission = $validated['commission'] ?? $employe->commission;
        
        if (isset($validated['password'])) {
            $employe->password = Hash::make($validated['password']);
        }
        
        $employe->save();

        return redirect()->route('users.index')
                        ->with('success', 'Employé mis à jour avec succès');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employe): RedirectResponse
    {
        $employe->delete();

        return redirect()->route('users.index')
                        ->with('success', 'Employé supprimé avec succès');
    }
    
    /**
     * Affiche le tableau de bord personnel de l'employé connecté.
     */
    public function tableauDeBord(Request $request): View
    {
        // Récupérer l'utilisateur connecté
        $employe = Auth::user();
        
        // Récupérer la semaine sélectionnée ou utiliser la semaine courante
        $semaine = $request->input('semaine', date('Y-\WW'));
        list($annee, $numSemaine) = explode('-W', $semaine);
        
        // Calculer les dates de début et fin de la semaine
        $debutSemaine = Carbon::now()->setISODate($annee, $numSemaine)->startOfWeek();
        $finSemaine = Carbon::now()->setISODate($annee, $numSemaine)->endOfWeek();
        
        // Semaine précédente pour comparaison
        $debutSemainePrecedente = (clone $debutSemaine)->subWeek();
        $finSemainePrecedente = (clone $finSemaine)->subWeek();
        
        // Récupérer les ventes de l'employé pour la semaine en cours
        $ventes = Commande::where(function($query) use ($employe) {
                $query->where('user_id', $employe->id);
            })
            ->whereBetween('date_commande', [$debutSemaine, $finSemaine])
            ->get();
            
        // Récupérer les ventes de la semaine précédente pour comparaison
        $ventesPrecedentes = Commande::where(function($query) use ($employe) {
                $query->where('user_id', $employe->id);
            })
            ->whereBetween('date_commande', [$debutSemainePrecedente, $finSemainePrecedente])
            ->get();
        
        // Calculer les statistiques
        $totalVentes = $ventes->sum(function($commande) {
            return $commande->prix_final ?? $commande->vehicule->prix_vente;
        });
        $totalVentesPrecedentes = $ventesPrecedentes->sum(function($commande) {
            return $commande->prix_final ?? $commande->vehicule->prix_vente;
        });
        $nbVehicules = $ventes->count();
        
        // Calculer la variation en pourcentage
        $variation = 0;
        if ($totalVentesPrecedentes > 0) {
            $variation = round((($totalVentes - $totalVentesPrecedentes) / $totalVentesPrecedentes) * 100, 2);
        }
        
        // Calculer le bénéfice total (prix de vente - prix d'achat)
        $beneficeTotal = $ventes->sum(function($commande) {
            $prixVente = $commande->prix_final ?? $commande->vehicule->prix_vente;
            return $prixVente - $commande->vehicule->prix_achat;
        });
        
        // Calculer la commission estimée sur le bénéfice (et non sur le CA)
        $commission = $beneficeTotal * $employe->getTauxCommission();
        
        // Préparer les données pour le graphique (4 dernières semaines)
        $historiqueVentes = [];
        for ($i = 3; $i >= 0; $i--) {
            $debutSemaineHisto = (clone $debutSemaine)->subWeeks($i);
            $finSemaineHisto = (clone $finSemaine)->subWeeks($i);
            
            $ventesHisto = Commande::where(function($query) use ($employe) {
                    $query->where('user_id', $employe->id);
                })
                ->whereBetween('date_commande', [$debutSemaineHisto, $finSemaineHisto])
                ->with('vehicule')
                ->get();
                
            $totalVentesHisto = $ventesHisto->sum(function($commande) {
                return $commande->prix_final ?? $commande->vehicule->prix_vente;
            });
            
            // Calculer le bénéfice total (prix de vente - prix d'achat)
            $beneficeTotalHisto = $ventesHisto->sum(function($commande) {
                $prixVente = $commande->prix_final ?? $commande->vehicule->prix_vente;
                return $prixVente - $commande->vehicule->prix_achat;
            });
            
            // Calculer la commission sur le bénéfice (et non sur le CA)
            $commissionHisto = $beneficeTotalHisto * $employe->getTauxCommission();
            
            $historiqueVentes[] = [
                'semaine' => 'S' . $debutSemaineHisto->format('W'),
                'ventes' => $totalVentesHisto,
                'commission' => $commissionHisto,
                'nb_vehicules' => $ventesHisto->count(),
                'debut' => $debutSemaineHisto->format('d/m/Y'),
                'fin' => $finSemaineHisto->format('d/m/Y'),
            ];
        }
        
        // Récupérer l'historique des commissions (10 dernières semaines)
        $historique = [];
        for ($i = 0; $i < 10; $i++) {
            $debutSemaineHisto = (clone $debutSemaine)->subWeeks($i);
            $finSemaineHisto = (clone $finSemaine)->subWeeks($i);
            
            $ventesHisto = Commande::where(function($query) use ($employe) {
                    $query->where('user_id', $employe->id);
                })
                ->whereBetween('date_commande', [$debutSemaineHisto, $finSemaineHisto])
                ->with('vehicule')
                ->get();
                
            if ($ventesHisto->count() > 0) {
                $totalVentesHisto = $ventesHisto->sum(function($commande) {
                    return $commande->prix_final ?? $commande->vehicule->prix_vente;
                });
                
                // Calculer le bénéfice total (prix de vente - prix d'achat)
                $beneficeTotalHisto = $ventesHisto->sum(function($commande) {
                    $prixVente = $commande->prix_final ?? $commande->vehicule->prix_vente;
                    return $prixVente - $commande->vehicule->prix_achat;
                });
                
                // Calculer la commission sur le bénéfice (et non sur le CA)
                $commissionHisto = $beneficeTotalHisto * $employe->getTauxCommission();
                
                $historique[] = (object) [
                    'semaine' => 'S' . $debutSemaineHisto->format('W') . ' (' . $debutSemaineHisto->format('d/m') . ' - ' . $finSemaineHisto->format('d/m') . ')',
                    'ventes' => $totalVentesHisto,
                    'nb_vehicules' => $ventesHisto->count(),
                    'commission' => $commissionHisto,
                    'paye' => $i > 1, // Exemple: les commissions des semaines précédentes sont payées
                ];
            }
        }
        
        // Récupérer les 5 dernières ventes
        $dernieresVentes = Commande::where(function($query) use ($employe) {
                $query->where('user_id', $employe->id);
            })
            ->with('vehicule')
            ->orderBy('date_commande', 'desc')
            ->limit(5)
            ->get();
        
        // Préparer les statistiques à passer à la vue
        $stats = [
            'ventes' => $totalVentes,
            'variation_ventes' => $variation,
            'nb_vehicules' => $nbVehicules,
            'benefice' => $beneficeTotal,
            'commission' => $commission,
            'objectif_ventes' => $employe->objectif_ventes > 0 ? $employe->objectif_ventes : 10000,
            'objectif_vehicules' => $employe->objectif_vehicules > 0 ? $employe->objectif_vehicules : 3,
            'objectif_commission' => $employe->objectif_commission > 0 ? $employe->objectif_commission : 1000,
            'historique' => $historiqueVentes
        ];
        
        return view('users.mon-tableau-de-bord', compact('employe', 'stats', 'historique', 'ventes', 'dernieresVentes'));
    }
}
