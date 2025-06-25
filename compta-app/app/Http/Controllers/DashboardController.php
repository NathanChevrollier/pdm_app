<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\Commande;
use App\Models\Employe;
use App\Models\User;
use App\Models\Vehicule;
use App\Models\Salaire;
use App\Models\Objectif;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Mois et année actuels et précédents
        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $lastMonthDate = $now->copy()->subMonth();
        $lastMonth = $lastMonthDate->month;
        $lastMonthYear = $lastMonthDate->year;
        
        // Semaine actuelle et précédente
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();
        $startOfLastWeek = $now->copy()->subWeek()->startOfWeek();
        $endOfLastWeek = $now->copy()->subWeek()->endOfWeek();
        
        // Commandes du mois actuel
        $currentMonthOrders = Commande::whereMonth('date_commande', $currentMonth)
            ->whereYear('date_commande', $currentYear)
            ->count();
            
        // Commandes du mois précédent
        $lastMonthOrders = Commande::whereMonth('date_commande', $lastMonth)
            ->whereYear('date_commande', $lastMonthYear)
            ->count();
        
        // Calcul du pourcentage de changement pour les commandes
        $ordersChange = 0;
        if ($lastMonthOrders > 0) {
            $ordersChange = round((($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1);
        }
        
        // Chiffre d'affaires du mois actuel
        $currentMonthRevenue = Commande::with('vehicule')
            ->whereMonth('date_commande', $currentMonth)
            ->whereYear('date_commande', $currentYear)
            ->get()
            ->sum(function($commande) {
                // Utiliser le prix final s'il existe, sinon le prix de vente
                if ($commande->prix_final) {
                    return $commande->prix_final;
                }
                return $commande->vehicule ? $commande->vehicule->prix_vente : 0;
            });
        
        // Chiffre d'affaires du mois précédent
        $lastMonthRevenue = Commande::with('vehicule')
            ->whereMonth('date_commande', $lastMonth)
            ->whereYear('date_commande', $lastMonthYear)
            ->get()
            ->sum(function($commande) {
                // Utiliser le prix final s'il existe, sinon le prix de vente
                if ($commande->prix_final) {
                    return $commande->prix_final;
                }
                return $commande->vehicule ? $commande->vehicule->prix_vente : 0;
            });
        
        // Calcul du pourcentage de changement pour le chiffre d'affaires
        $revenueChange = 0;
        if ($lastMonthRevenue > 0) {
            $revenueChange = round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
        }
        
        // Véhicules disponibles (non vendus)
        $availableVehicles = Vehicule::whereDoesntHave('commandes', function($query) {
            $query->where('statut', 'completed');
        })->count();
        
        // Total des véhicules
        $totalVehicles = Vehicule::count();
        
        // Employés actifs
        $activeEmployees = User::whereNotNull('statut')->where('statut', '!=', '')->count();
        
        // Total des employés
        $totalEmployees = User::whereNotNull('statut')->count();
        
        // Commandes de la semaine actuelle
        $currentWeekOrders = Commande::whereBetween('date_commande', [$startOfWeek, $endOfWeek])->count();
        
        // Commandes de la semaine précédente
        $lastWeekOrders = Commande::whereBetween('date_commande', [$startOfLastWeek, $endOfLastWeek])->count();
        
        // Calcul du pourcentage de changement pour les commandes hebdomadaires
        $weeklyOrdersChange = 0;
        if ($lastWeekOrders > 0) {
            $weeklyOrdersChange = round((($currentWeekOrders - $lastWeekOrders) / $lastWeekOrders) * 100, 1);
        }
        
        // Chiffre d'affaires de la semaine actuelle
        $currentWeekRevenue = Commande::with('vehicule')
            ->whereBetween('date_commande', [$startOfWeek, $endOfWeek])
            ->get()
            ->sum(function($commande) {
                // Utiliser le prix final s'il existe, sinon le prix de vente
                if ($commande->prix_final) {
                    return $commande->prix_final;
                }
                return $commande->vehicule ? $commande->vehicule->prix_vente : 0;
            });
        
        // Chiffre d'affaires de la semaine précédente
        $lastWeekRevenue = Commande::with('vehicule')
            ->whereBetween('date_commande', [$startOfLastWeek, $endOfLastWeek])
            ->get()
            ->sum(function($commande) {
                // Utiliser le prix final s'il existe, sinon le prix de vente
                if ($commande->prix_final) {
                    return $commande->prix_final;
                }
                return $commande->vehicule ? $commande->vehicule->prix_vente : 0;
            });
        
        // Calcul du pourcentage de changement pour le chiffre d'affaires hebdomadaire
        $weeklyRevenueChange = 0;
        if ($lastWeekRevenue > 0) {
            $weeklyRevenueChange = round((($currentWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100, 1);
        }
        
        // Top vendeurs de la semaine (exclure les administrateurs)
        $topSellers = User::whereNotNull('statut')
            ->where('statut', '!=', '')
            ->where('statut', '!=', 'admin') // Exclure les administrateurs
            ->withCount(['commandes' => function($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('date_commande', [$startOfWeek, $endOfWeek]);
            }])
            ->orderByDesc('commandes_count')
            ->take(5)
            ->get()
            ->map(function($user) use ($startOfWeek, $endOfWeek) {
                // Calculer le montant total des ventes pour ce vendeur
                $totalVentes = Commande::where('user_id', $user->id)
                    ->whereBetween('date_commande', [$startOfWeek, $endOfWeek])
                    ->with('vehicule')
                    ->get()
                    ->sum(function($commande) {
                        return $commande->prix_final ?: ($commande->vehicule ? $commande->vehicule->prix_vente : 0);
                    });
                    
                return [
                    'id' => $user->id,
                    'nom' => $user->nom . ' ' . $user->prenom,
                    'email' => $user->email,
                    'role' => $user->statut, // Utiliser statut au lieu de role
                    'ventes' => $user->commandes_count,
                    'montant' => $totalVentes,
                ];
            });
        
        // Récupérer les statistiques de base
        $total_commandes_actuel = Commande::count();
        $total_commandes_precedent = Commande::where('date_commande', '<', Carbon::now()->subMonth())->count();
        $orders_change = $total_commandes_precedent > 0 ? 
            round((($total_commandes_actuel - $total_commandes_precedent) / $total_commandes_precedent) * 100) : 0;
        
        $chiffre_affaires_actuel = Commande::join('vehicules', 'commandes.vehicule_id', '=', 'vehicules.id')
            ->sum(DB::raw('COALESCE(commandes.prix_final, vehicules.prix_vente)'));
            
        $chiffre_affaires_precedent = Commande::join('vehicules', 'commandes.vehicule_id', '=', 'vehicules.id')
            ->where('commandes.date_commande', '<', Carbon::now()->subMonth())
            ->sum(DB::raw('COALESCE(commandes.prix_final, vehicules.prix_vente)'));
            
        $revenue_change = $chiffre_affaires_precedent > 0 ? 
            round((($chiffre_affaires_actuel - $chiffre_affaires_precedent) / $chiffre_affaires_precedent) * 100) : 0;
        
        // Calcul du bénéfice brut (prix de vente - prix d'achat)
        $benefice_brut = Commande::join('vehicules', 'commandes.vehicule_id', '=', 'vehicules.id')
            ->whereMonth('commandes.date_commande', Carbon::now()->month)
            ->selectRaw('SUM(COALESCE(commandes.prix_final, vehicules.prix_vente) - vehicules.prix_achat) as benefice')
            ->value('benefice') ?? 0;
            
        // Récupération des salaires payés ce mois-ci
        $salaires_mois = Salaire::whereMonth('created_at', Carbon::now()->month)
            ->where('est_paye', 1)
            ->sum('commission');
            
        // Calcul du bénéfice net (bénéfice brut - salaires)
        $benefice_total = $benefice_brut - $salaires_mois;
        
        // Récupérer les objectifs globaux ou utiliser les valeurs par défaut
        $objectifs = Objectif::getActiveObjectifs();
        $objectif_benefice = $objectifs->objectif_benefice;
        
        // Calcul des commissions totales pour le mois en cours
        $commissions_mois = Salaire::whereMonth('created_at', Carbon::now()->month)
            ->sum('commission');
        
        // Données pour le graphique des commandes de la semaine
        $commandesSemaine = [];
        $joursSemaine = [];
        
        // Récupérer les données pour chaque jour de la semaine
        for ($i = 0; $i < 7; $i++) {
            $jour = $startOfWeek->copy()->addDays($i);
            $joursSemaine[] = $jour->format('d/m');
            
            $commandesSemaine[] = Commande::whereDate('date_commande', $jour)->count();
        }
        
        // Données pour le graphique des commandes par utilisateur
        $commandesParUtilisateur = User::where('statut', '!=', 'inactif')
            ->withCount(['commandes' => function($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('date_commande', [$startOfWeek, $endOfWeek]);
            }])
            ->having('commandes_count', '>', 0)
            ->orderBy('commandes_count', 'desc')
            ->take(10)
            ->get()
            ->map(function($user) {
                return [
                    'nom' => $user->nom . ' ' . $user->prenom,
                    'commandes' => $user->commandes_count
                ];
            });
            
        // Calcul du bénéfice brut de la semaine en cours
        $benefice_semaine_brut = Commande::join('vehicules', 'commandes.vehicule_id', '=', 'vehicules.id')
            ->whereBetween('commandes.date_commande', [$startOfWeek, $endOfWeek])
            ->selectRaw('SUM(commandes.prix_final - vehicules.prix_achat) as benefice')
            ->value('benefice') ?? 0;
            
        // Récupération des salaires payés cette semaine
        $salaires_semaine = Salaire::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->where('est_paye', 1)
            ->sum('commission');
            
        // Calcul du bénéfice net de la semaine (bénéfice brut - salaires)
        $benefice_semaine = $benefice_semaine_brut - $salaires_semaine;
            
        $stats = [
            // Données générales
            'commandes_aujourd_hui' => Commande::whereDate('date_commande', Carbon::today())->count(),
            'total_commandes' => $total_commandes_actuel,
            'chiffre_affaires' => $chiffre_affaires_actuel,
            'revenue_change' => $revenue_change,
            
            // Données des véhicules
            'total_vehicles' => $totalVehicles,
            'available_vehicles' => $availableVehicles,
            
            // Données des employés
            'active_employees' => $activeEmployees,
            'total_employees' => $totalEmployees,
            
            // Données financières
            'benefice_total' => $benefice_total,
            'objectif_benefice' => $objectif_benefice,
            'objectif_ventes' => $objectifs->objectif_ventes,
            'benefice_semaine' => $benefice_semaine,
            'total_commissions' => $commissions_mois,
            
            // Top vendeur
            'top_vendeurs' => $topSellers->isNotEmpty() ? $topSellers->first()['nom'] : 'Aucun',
            
            // Données des commandes
            'monthly_orders' => $currentMonthOrders,
            'orders_change' => $ordersChange,
            'weekly_orders' => $currentWeekOrders,
            'weekly_orders_change' => $weeklyOrdersChange,
            'weekly_revenue' => $currentWeekRevenue,
            'weekly_revenue_change' => $weeklyRevenueChange,
            
            // Données pour les graphiques
            'week_start' => $startOfWeek->format('d/m/Y'),
            'week_end' => $endOfWeek->format('d/m/Y'),
            'jours_semaine' => $joursSemaine,
            'commandes_semaine' => $commandesSemaine,
            'commandes_par_utilisateur' => $commandesParUtilisateur,
        ];

        // Préparer les données pour le graphique des commandes
        $currentYear = now()->year;
        $previousYear = $currentYear - 1;
        
        // Récupérer les commandes des deux dernières années groupées par mois
        $ordersByMonth = Commande::select(
                DB::raw('YEAR(date_commande) as year'),
                DB::raw('MONTH(date_commande) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('date_commande', '>=', $previousYear)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Initialiser les tableaux de données pour les deux années
        $chartData = [
            'labels' => [],
            'data' => [
                $currentYear => array_fill(0, 12, 0),
                $previousYear => array_fill(0, 12, 0),
            ],
            'currentYear' => $currentYear,
            'previousYear' => $previousYear
        ];
        
        // Remplir les données du graphique
        foreach ($ordersByMonth as $order) {
            $year = $order->year;
            $month = $order->month - 1; // Pour l'index du tableau (0-11)
            
            if (isset($chartData['data'][$year])) {
                $chartData['data'][$year][$month] = $order->count;
            }
        }
        
        // Définir les étiquettes des mois (noms en français)
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $date = Carbon::create(null, $i, 1);
            $months[] = ucfirst($date->locale('fr')->monthName);
        }
        $chartData['labels'] = $months;
        
        // Récupérer les commandes récentes avec les relations nécessaires
        $recentOrders = Commande::with(['vehicule', 'employe'])
            ->latest('date_commande')
            ->take(5)
            ->get()
            ->map(function ($commande) {
                return [
                    'id' => $commande->id,
                    'customer' => $commande->client_nom,
                    'vehicle' => $commande->vehicule->modele ?? 'N/A',
                    'date' => $commande->date_commande->format('d/m/Y'),
                    'amount' => $commande->prix_final ?? ($commande->vehicule->prix_vente ?? 0),
                    'status' => $commande->statut,
                    'status_label' => $this->getStatusLabel($commande->statut),
                    'status_class' => $this->getStatusClass($commande->statut)
                ];
            })
            ->toArray();
        
        // Si aucune commande n'existe, utiliser des données factices pour la démo
        if (empty($recentOrders)) {
            $recentOrders = [
                [
                    'id' => '#4580',
                    'customer' => 'Jean Dupont',
                    'vehicle' => 'Toyota RAV4',
                    'date' => now()->subDays(2)->format('d/m/Y'),
                    'amount' => 18500000,
                    'status' => 'completed',
                    'status_label' => 'Terminée',
                    'status_class' => 'bg-label-success'
                ],
                // ... autres commandes factices ...
            ];
        }

        // Récupération des activités récentes depuis la base de données
        $recentActivities = Activite::with('user')
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(function($activite) {
                // Déterminer l'icône et la couleur en fonction du type d'activité
                $icon = 'bx-bell';
                $color = 'secondary';
                
                switch($activite->type) {
                    case 'connexion':
                        $icon = 'bx-log-in';
                        $color = 'primary';
                        break;
                    case 'creation':
                        $icon = 'bx-plus-circle';
                        $color = 'success';
                        break;
                    case 'modification':
                        $icon = 'bx-edit';
                        $color = 'info';
                        break;
                    case 'suppression':
                        $icon = 'bx-trash';
                        $color = 'danger';
                        break;
                    case 'paiement':
                        $icon = 'bx-money';
                        $color = 'warning';
                        break;
                }
                
                return [
                    'id' => $activite->id,
                    'type' => $activite->type,
                    'title' => ucfirst($activite->titre),
                    'description' => $activite->description,
                    'date' => $activite->created_at,
                    'user' => $activite->user ? $activite->user->nom . ' ' . $activite->user->prenom : 'Système',
                    'icon' => $icon,
                    'color' => $color
                ];
            })->toArray();
        
        // Récupérer les employés récents (exclure les administrateurs)
        $derniers_employes = User::employesOnly()
            ->latest('created_at')
            ->take(5)
            ->get();
            
        // Récupérer les véhicules récents
        $derniers_vehicules = Vehicule::latest('created_at')
            ->take(5)
            ->get();
            
        return view('dashboard', [
            'stats' => $stats,
            'chartData' => $chartData,
            'recentOrders' => $recentOrders,
            'recentActivities' => $recentActivities,
            'topSellers' => $topSellers,
            'derniers_employes' => $derniers_employes,
            'derniers_vehicules' => $derniers_vehicules,
        ]);
    }
    
    /**
     * Retourne le libellé du statut en fonction de la valeur
     *
     * @param string $status
     * @return string
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'En attente',
            'processing' => 'En cours',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée',
        ];
        
        return $labels[$status] ?? ucfirst($status);
    }
    
    /**
     * Retourne la classe CSS du statut
     *
     * @param string $status
     * @return string
     */
    private function getStatusClass($status)
    {
        $classes = [
            'pending' => 'bg-label-secondary',
            'processing' => 'bg-label-warning',
            'completed' => 'bg-label-success',
            'cancelled' => 'bg-label-danger',
        ];
        
        return $classes[$status] ?? 'bg-label-secondary';
    }
}
