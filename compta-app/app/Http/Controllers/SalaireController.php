<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Commande;
use App\Models\Salaire;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class SalaireController extends Controller
{
    /**
     * Affiche le tableau des salaires des employés
     * Calcule automatiquement les salaires pour la semaine sélectionnée
     */
    public function index(Request $request): View
    {
        // Récupérer la semaine sélectionnée ou utiliser la semaine actuelle
        $week = $request->input('week', Carbon::now()->format('Y-\WW'));
        $date = Carbon::now();
        
        if ($week) {
            try {
                // Utiliser un format plus fiable pour la semaine
                list($year, $weekNumber) = explode('-W', $week);
                $date = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
            } catch (\Exception $e) {
                // En cas d'erreur, utiliser la date actuelle
                $date = Carbon::now();
            }
        }
        
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();
        
        // Récupérer tous les employés avec leurs commandes de la semaine
        $employes = User::whereNotNull('statut')->with(['commandes' => function($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('date_commande', [$startOfWeek, $endOfWeek])
                  ->with('vehicule');
        }])->get();
        
        // Calculer les salaires automatiquement pour la semaine sélectionnée
        $salaires = $employes->map(function($employe) {
            $totalVentes = $employe->commandes->sum(function($commande) {
                // Utiliser le prix final s'il existe, sinon revenir au prix de vente
                return $commande->prix_final ?? $commande->vehicule->prix_vente;
            });
            
            $beneficeBrut = $employe->commandes->sum(function($commande) {
                // Calculer le bénéfice brut en utilisant le prix final (après réduction)
                $prixVente = $commande->prix_final ?? $commande->vehicule->prix_vente;
                return $prixVente - $commande->vehicule->prix_achat;
            });
            
            // Calculer le bénéfice net (après déductions de taxes et frais)
            // Estimation des taxes et frais (20% du bénéfice brut)
            $taxes = $beneficeBrut * 0.20;
            $beneficeNet = $beneficeBrut - $taxes;
            
            // Calculer la commission sur le bénéfice NET (et non sur le bénéfice brut)
            $totalCommissions = $beneficeNet * $employe->getTauxCommission();
            
            return [
                'employe' => $employe,
                'nb_commandes' => $employe->commandes->count(),
                'total_ventes' => $totalVentes,
                'benefice_brut' => $beneficeBrut,
                'taxes' => $taxes,
                'benefice_net' => $beneficeNet,
                'total_commissions' => $totalCommissions,
                'salaire_net' => $totalCommissions // Pour l'instant, le salaire net = total des commissions
            ];
        });
        
        // Générer les semaines disponibles pour le filtre
        $weeks = [];
        $startDate = Carbon::now()->subMonths(6)->startOfWeek(); // Afficher les 6 derniers mois
        $endDate = Carbon::now()->addMonths(1)->endOfWeek(); // Et le mois suivant
        
        while ($startDate->lte($endDate)) {
            $weekNumber = $startDate->format('Y-\WW');
            $weeks[$weekNumber] = 'Semaine ' . $startDate->weekOfYear . ' - ' . $startDate->format('d/m/Y');
            $startDate->addWeek();
        }
        
        // Calculer les totaux
        $beneficeBrut = $salaires->sum('benefice_brut');
        $taxes = $salaires->sum('taxes');
        $beneficeNet = $salaires->sum('benefice_net');
        $totalCommissions = $salaires->sum('total_commissions');
        
        // Calcul du bénéfice réel après déduction des commissions
        $beneficeApresCommissions = $beneficeNet - $totalCommissions;
        
        // Récupérer les déductions de taxes depuis la session
        $deductionsTaxes = session('deductions_taxes', 0);
        
        // Calcul des taxes (5% sur le bénéfice après commissions)
        $taxes = $beneficeApresCommissions * 0.05; // 5% de taxes sur le bénéfice
        $taxesNettes = max(0, $taxes - $deductionsTaxes); // Déduire les déductions, minimum 0
        
        // Bénéfice net final après commissions et taxes
        $beneficeNetFinal = $beneficeApresCommissions - $taxesNettes;
        
        // Récupérer les objectifs depuis la session ou utiliser des valeurs par défaut
        $objectifVentes = session('objectif_ventes', 100000); // 100 000 € par défaut
        $objectifBenefice = session('objectif_benefice', 30000); // 30 000 € par défaut
        
        $totaux = [
            'brut' => $salaires->sum('total_ventes'),
            'benefice_brut' => $beneficeBrut,
            'taxes_internes' => $taxes, // Taxes internes (20% du bénéfice brut)
            'benefice_net' => $beneficeNet, // Bénéfice net après taxes internes
            'commissions' => $totalCommissions,
            'benefice_apres_commissions' => $beneficeApresCommissions,
            'taxes' => $taxes,
            'deductions_taxes' => $deductionsTaxes,
            'taxes_nettes' => $taxesNettes,
            'benefice_net_final' => $beneficeNetFinal, // Bénéfice net final après commissions et taxes externes
            'net' => $totalCommissions, // Total des commissions à payer aux employés
            'objectif_ventes' => $objectifVentes,
            'objectif_benefice' => $objectifBenefice
        ];
        
        return view('salaires.index', [
            'salaires' => $salaires,
            'currentWeek' => $date->format('Y-\WW'),
            'weeks' => $weeks,
            'startOfWeek' => $startOfWeek->format('d/m/Y'),
            'endOfWeek' => $endOfWeek->format('d/m/Y'),
            'totaux' => $totaux
        ]);
    }

    /**
     * Affiche le formulaire de création d'un nouveau salaire
     */
    public function create(): View
    {
        $employes = User::employesOnly()->get(); // Exclure les administrateurs
        return view('salaires.create', compact('employes'));
    }

    /**
     * Enregistre un nouveau salaire
     */
    public function store(Request $request)
    {
        // Cette méthode sera implémentée plus tard
        return redirect()->route('salaires.index')->with('success', 'Salaire créé avec succès.');
    }

    /**
     * Affiche les détails d'un salaire
     */
    public function show($id)
    {
        // Pour l'instant, on redirige vers la liste des salaires
        return redirect()->route('salaires.index');
    }

    /**
     * Affiche le formulaire d'édition d'un salaire
     */
    public function edit($id)
    {
        // Pour l'instant, on redirige vers la liste des salaires
        return redirect()->route('salaires.index');
    }

    /**
     * Met à jour un salaire
     */
    public function update(Request $request, $id)
    {
        // Cette méthode sera implémentée plus tard
        return redirect()->route('salaires.index')->with('success', 'Salaire mis à jour avec succès.');
    }

    /**
     * Supprime un salaire
     */
    public function destroy($id)
    {
        // Cette méthode sera implémentée plus tard
        return redirect()->route('salaires.index')->with('success', 'Salaire supprimé avec succès.');
    }

    /**
     * Génère les salaires de la semaine en cours
     */
    public function generer(Request $request)
    {
        // Vérifier que l'utilisateur est admin ou manager
        if (!auth()->user() || !in_array(auth()->user()->statut, ['admin', 'manager'])) {
            return redirect()->route('salaires.index')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour générer les salaires.');
        }
        
        // Récupérer la semaine sélectionnée ou utiliser la semaine actuelle
        $week = $request->input('week', Carbon::now()->format('Y-\WW'));
        $date = Carbon::now();
        
        if ($week) {
            try {
                // Utiliser un format plus fiable pour la semaine
                list($year, $weekNumber) = explode('-W', $week);
                $date = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
            } catch (\Exception $e) {
                // En cas d'erreur, utiliser la date actuelle
                $date = Carbon::now();
            }
        }
        
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();
        
        // Récupérer tous les employés avec leurs commandes de la semaine (exclure les administrateurs)
        $employes = User::employesOnly()->get();
        $count = 0;
        
        foreach ($employes as $employe) {
            // Récupérer les commandes de l'employé pour la semaine
            $commandes = Commande::where('user_id', $employe->id)
                ->whereBetween('date_commande', [$startOfWeek, $endOfWeek])
                ->with('vehicule')
                ->get();
            
            if ($commandes->count() > 0) {
                $count++;
                
                // Calculer le total des ventes en tenant compte des réductions
                $totalVentes = $commandes->sum(function($commande) {
                    // Utiliser le prix final s'il existe, sinon revenir au prix de vente
                    return $commande->prix_final ?? $commande->vehicule->prix_vente;
                });
                
                // Calculer le bénéfice total (prix_final - prix_achat)
                $beneficeTotal = $commandes->sum(function($commande) {
                    // Utiliser le prix final s'il existe, sinon revenir au prix de vente
                    $prixVente = $commande->prix_final ?? $commande->vehicule->prix_vente;
                    return $prixVente - $commande->vehicule->prix_achat;
                });
                
                // Calculer la commission selon le taux de l'employé
                $commission = $beneficeTotal * $employe->getTauxCommission();
                
                // Enregistrer ou mettre à jour le salaire dans la base de données
                // Note: Cette partie nécessite un modèle Salaire qui n'est pas encore implémenté
                // Pour l'instant, nous allons simplement enregistrer les données en session
                session()->flash('salaires.' . $employe->id, [
                    'employe_id' => $employe->id,
                    'employe_nom' => $employe->nom . ' ' . $employe->prenom,
                    'semaine' => $week,
                    'nb_commandes' => $commandes->count(),
                    'total_ventes' => $totalVentes,
                    'benefice_total' => $beneficeTotal,
                    'commission' => $commission,
                    'date_calcul' => now(),
                    'periode_debut' => $startOfWeek->format('Y-m-d'),
                    'periode_fin' => $endOfWeek->format('Y-m-d'),
                ]);
            }
        }
        
        return redirect()->route('salaires.index', ['week' => $week])
            ->with('success', $count . ' salaires ont été générés avec succès pour la semaine du ' . 
                   $startOfWeek->format('d/m/Y') . ' au ' . $endOfWeek->format('d/m/Y'));
    }

    /**
     * Affiche les fiches de paie
     */
    public function fiches(Request $request)
    {
        // Récupérer la semaine sélectionnée ou utiliser la semaine actuelle
        $week = $request->input('week', Carbon::now()->format('Y-\WW'));
        $date = Carbon::now();
        
        if ($week) {
            try {
                list($year, $weekNumber) = explode('-W', $week);
                $date = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
            } catch (\Exception $e) {
                $date = Carbon::now();
            }
        }
        
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();
        
        // Récupérer tous les employés avec leurs commandes de la semaine
        $employes = User::whereNotNull('statut')->with(['commandes' => function($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('date_commande', [$startOfWeek, $endOfWeek])
                  ->with('vehicule');
        }])->get();
        
        // Générer les semaines disponibles pour le filtre
        $weeks = [];
        $startDate = Carbon::now()->subMonths(3)->startOfWeek(); // 3 mois en arrière
        $endDate = Carbon::now()->endOfWeek();
        
        while ($startDate->lte($endDate)) {
            $weekNumber = $startDate->format('Y-\WW');
            $weeks[$weekNumber] = 'Semaine ' . $startDate->weekOfYear . ' - ' . $startDate->format('d/m/Y');
            $startDate->addWeek();
        }
        
        return view('salaires.fiches', [
            'employes' => $employes,
            'currentWeek' => $date->format('Y-\WW'),
            'weeks' => $weeks,
            'startOfWeek' => $startOfWeek->format('d/m/Y'),
            'endOfWeek' => $endOfWeek->format('d/m/Y')
        ]);
    }

    /**
     * Affiche les statistiques des salaires
     */
    public function statistiques()
    {
        // Récupérer les données des 3 derniers mois (période plus courte pour optimiser la requête)
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        // Récupérer tous les employés avec leurs commandes
        $employes = User::whereNotNull('statut')->with(['commandes' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('date_commande', [$startDate, $endDate])
                  ->with('vehicule');
        }])->get();
        
        // Note: Les données pour les graphiques ne sont plus nécessaires car ils ont été déplacés
        // vers le tableau de bord principal
        
        return view('salaires.statistiques', [
            'employes' => $employes
        ]);
    }

    /**
     * Marque un salaire comme payé
     */
    public function payer($id)
    {
        // Logique pour marquer un salaire comme payé
        // Pour l'instant, on redirige vers la liste des salaires avec un message de succès
        return redirect()->route('salaires.index')->with('success', 'Le salaire a été marqué comme payé avec succès.');
    }
    
    /**
     * Marque un salaire comme payé via le formulaire
     */
    public function marquerPaye(Request $request)
    {
        // Vérifier que l'utilisateur est admin ou manager
        if (!auth()->user() || !in_array(auth()->user()->statut, ['admin', 'manager'])) {
            return redirect()->route('salaires.index')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour modifier les commissions.');
        }
        
        // Récupérer les données du formulaire
        $userId = $request->input('user_id');
        $week = $request->input('week');
        
        // Vérifier que les données sont valides
        if (!$userId || !$week) {
            return redirect()->route('salaires.index')->with('error', 'Données invalides pour marquer le salaire comme payé.');
        }
        
        // Récupérer l'employé
        $employe = User::find($userId);
        if (!$employe) {
            return redirect()->route('salaires.index')->with('error', 'Employé non trouvé.');
        }
        
        try {
            // Calculer les données du salaire
            if ($week) {
                list($year, $weekNumber) = explode('-W', $week);
                $date = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
            } else {
                $date = Carbon::now();
            }
            
            $startOfWeek = $date->copy()->startOfWeek();
            $endOfWeek = $date->copy()->endOfWeek();
            
            // Récupérer les commandes de l'employé pour cette semaine
            $commandes = Commande::where('user_id', $userId)
                ->whereBetween('date_commande', [$startOfWeek, $endOfWeek])
                ->with('vehicule')
                ->get();
            
            $totalVentes = $commandes->sum(function($commande) {
                return $commande->prix_final ?? $commande->vehicule->prix_vente;
            });
            
            $beneficeTotal = $commandes->sum(function($commande) {
                $prixVente = $commande->prix_final ?? $commande->vehicule->prix_vente;
                return $prixVente - $commande->vehicule->prix_achat;
            });
            
            // Calculer la commission selon le taux de l'employé
            $commission = $beneficeTotal * $employe->getTauxCommission();
            
            // Chercher si un enregistrement de salaire existe déjà pour cet employé et cette période
            $salaire = Salaire::where('user_id', $userId)
                ->where('periode_debut', $startOfWeek)
                ->where('periode_fin', $endOfWeek)
                ->first();
                
            // Si aucun salaire n'existe, en créer un nouveau
            if (!$salaire) {
                $salaire = new Salaire();
                $salaire->user_id = $userId;
                $salaire->periode_debut = $startOfWeek;
                $salaire->periode_fin = $endOfWeek;
            }
            
            // Mettre à jour l'enregistrement
            $salaire->montant_base = 0; // Salaire de base (à ajuster selon vos besoins)
            $salaire->commission = $commission;
            $salaire->montant_final = $commission; // Montant final = commission pour l'instant
            $salaire->est_paye = true;
            $salaire->date_paiement = now();
            $salaire->save();
            
            // Forcer l'actualisation des données en session pour éviter les problèmes de cache
            $request->session()->forget('salaires_data');
            
            // Rediriger avec un paramètre timestamp pour forcer le rechargement complet
            return redirect()->route('salaires.index', ['week' => $week, 'refresh' => time()])
                ->with('success', 'Le salaire de ' . $employe->nom . ' ' . $employe->prenom . ' a été marqué comme payé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('salaires.index')->with('error', 'Une erreur est survenue lors du marquage du salaire comme payé: ' . $e->getMessage());
        }
    }
    
    /**
     * Mettre à jour les déductions de taxes
     */
    public function updateDeductionsTaxes(Request $request)
    {
        // Vérifier les permissions (seuls admin, gérant, co-gérant peuvent modifier les déductions)
        if (!auth()->user() || !in_array(auth()->user()->statut, ['admin', 'gerant', 'co-gerant'])) {
            return redirect()->back()
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour effectuer cette action.');
        }

        // Valider les données
        $validated = $request->validate([
            'deductions_taxes' => 'required|numeric|min:0',
        ]);

        // Stocker les déductions dans la session
        session(['deductions_taxes' => $validated['deductions_taxes']]);
        
        // Forcer l'enregistrement de la session pour s'assurer que les données sont disponibles immédiatement
        session()->save();

        // Rediriger avec un message de succès
        return redirect()->route('salaires.index')
            ->with('success', 'Les déductions de taxes ont été mises à jour avec succès.');
    }
    
    /**
     * Mettre à jour les objectifs globaux
     */
    public function updateObjectifs(Request $request)
    {
        // Vérifier les permissions (seuls admin, gérant, co-gérant peuvent modifier les objectifs)
        if (!auth()->user() || !in_array(auth()->user()->statut, ['admin', 'gerant', 'co-gerant'])) {
            return redirect()->back()
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour effectuer cette action.');
        }

        // Valider les données (suppression des champs véhicules et commissions)
        $validated = $request->validate([
            'objectif_ventes' => 'required|numeric|min:0',
            'objectif_benefice' => 'required|numeric|min:0',
        ]);

        // Stocker les objectifs dans la session avec les mêmes clés que celles utilisées dans la méthode index
        session([
            'objectif_ventes' => $validated['objectif_ventes'],
            'objectif_benefice' => $validated['objectif_benefice']
        ]);
        
        // Forcer le flash des données de session pour s'assurer qu'elles sont disponibles immédiatement
        session()->save();

        // Rediriger avec un message de succès
        return redirect()->route('salaires.index')
            ->with('success', 'Les objectifs globaux ont été mis à jour avec succès.');
    }
}
