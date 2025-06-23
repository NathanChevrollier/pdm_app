<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\User;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    /**
     * Affiche la liste des activités avec filtrage et tri
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Activite::with('user');
        
        // Filtre par type d'activité
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        // Filtre par utilisateur
        if ($request->has('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }
        
        // Filtre par période
        if ($request->has('period')) {
            $period = $request->period;
            $now = now();
            
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [
                        $now->startOfWeek()->toDateTimeString(),
                        $now->endOfWeek()->toDateTimeString()
                    ]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', $now->year);
                    break;
            }
        }
        
        // Recherche par mot-clé
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Tri des résultats
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        
        $query->orderBy($sortField, $sortDirection);
        
        $activites = $query->paginate(20)->withQueryString();
        
        // Données pour les filtres
        $types = Activite::select('type')
            ->distinct()
            ->pluck('type');
            
        $users = User::whereIn('id', function($query) {
                $query->select('user_id')
                    ->from('activites')
                    ->groupBy('user_id');
            })
            ->select('id', \DB::raw("CONCAT(nom, ' ', prenom) as nom_complet"))
            ->pluck('nom_complet', 'id');
        
        return view('activites.index', compact(
            'activites',
            'types',
            'users',
            'sortField',
            'sortDirection'
        ));
    }

    /**
     * Exporte les données des activités
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        // Construction de la requête avec les mêmes filtres que la méthode index
        $query = Activite::with('user');
        
        // Filtre par type d'activité
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        // Filtre par utilisateur
        if ($request->has('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }
        
        // Filtre par période
        if ($request->has('period')) {
            $period = $request->period;
            $now = now();
            
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [
                        $now->startOfWeek()->toDateTimeString(),
                        $now->endOfWeek()->toDateTimeString()
                    ]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', $now->year);
                    break;
            }
        }
        
        // Recherche par mot-clé
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Tri des résultats
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        
        $query->orderBy($sortField, $sortDirection);
        
        // Récupération des activités
        $activites = $query->get();
        
        // Pour l'instant, redirection avec un message de succès
        // Dans une implémentation future, on pourrait générer un CSV ou Excel
        return redirect()->route('activites.index')
            ->with('success', 'Les données des activités ont été exportées avec succès.');
    }

    /**
     * Affiche les détails d'une activité spécifique
     *
     * @param  \App\Models\Activite  $activite
     * @return \Illuminate\View\View
     */
    public function show(Activite $activite)
    {
        // Chargement des relations nécessaires
        $activite->load('user');
        
        return view('activites.show', compact('activite'));
    }

    /**
     * Nettoie le journal des activités
     * Accessible uniquement aux administrateurs et gérants
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clear(Request $request)
    {
        // Vérifier si l'utilisateur est administrateur ou gérant
        if (!auth()->user() || !in_array(auth()->user()->statut, ['admin', 'manager'])) {
            return redirect()->route('activites.index')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour effectuer cette action.');
        }
        
        // Suppression de toutes les activités
        $count = Activite::count();
        Activite::truncate();
        
        return redirect()->route('activites.index')
            ->with('success', $count . ' activités ont été supprimées du journal.');
    }
    
    /**
     * Supprime une activité spécifique
     *
     * @param  \App\Models\Activite  $activite
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activite $activite)
    {
        // Suppression de l'activité
        $activite->delete();
        
        return redirect()->route('activites.index')
            ->with('success', 'L\'activité #' . $activite->id . ' a été supprimée avec succès.');
    }
}
