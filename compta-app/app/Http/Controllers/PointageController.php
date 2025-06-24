<?php

namespace App\Http\Controllers;

use App\Models\Pointage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\View\View;

class PointageController extends Controller
{
    /**
     * Affiche la page principale de la badgeuse
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
        
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        
        // Vérifier si l'utilisateur est un manager, co-gérant ou gérant
        $isManager = in_array($user->statut, ['admin', 'manager', 'gerant', 'co-gerant']);
        
        // Récupérer les pointages de l'utilisateur pour la semaine sélectionnée
        $pointagesUtilisateur = Pointage::getPointagesSemaine($user->id, $startOfWeek, $endOfWeek);
        
        // Calculer le temps total de présence de l'utilisateur pour la semaine
        $tempsPresenceUtilisateur = Pointage::getTempsPresenceSemaine($user->id, $startOfWeek, $endOfWeek);
        
        // Récupérer le pointage en cours de l'utilisateur
        $pointageEnCours = Pointage::getPointageEnCours($user->id);
        
        // Si l'utilisateur est un manager, récupérer les pointages de tous les employés
        $pointagesEmployes = [];
        $tempsPresenceEmployes = [];
        $employes = [];
        
        if ($isManager) {
            $employes = User::whereNotNull('statut')->get();
            
            foreach ($employes as $employe) {
                $pointagesEmployes[$employe->id] = Pointage::getPointagesSemaine($employe->id, $startOfWeek, $endOfWeek);
                $tempsPresenceEmployes[$employe->id] = Pointage::getTempsPresenceSemaine($employe->id, $startOfWeek, $endOfWeek);
            }
        }
        
        // Générer les semaines disponibles pour le filtre
        $weeks = [];
        $startDate = Carbon::now()->subMonths(3)->startOfWeek(); // Afficher les 3 derniers mois
        $endDate = Carbon::now()->addMonths(1)->endOfWeek(); // Et le mois suivant
        
        while ($startDate->lte($endDate)) {
            $weekNumber = $startDate->format('Y-\WW');
            $weeks[$weekNumber] = 'Semaine ' . $startDate->weekOfYear . ' - ' . $startDate->format('d/m/Y');
            $startDate->addWeek();
        }
        
        return view('pointages.index', compact(
            'user', 
            'isManager', 
            'pointagesUtilisateur', 
            'tempsPresenceUtilisateur', 
            'pointageEnCours',
            'pointagesEmployes',
            'tempsPresenceEmployes',
            'employes',
            'week',
            'weeks',
            'startOfWeek',
            'endOfWeek'
        ));
    }
    
    /**
     * Enregistre un nouveau pointage (entrée)
     */
    public function entree(Request $request)
    {
        $user = Auth::user();
        
        // Vérifier s'il n'y a pas déjà un pointage en cours
        $pointageEnCours = Pointage::getPointageEnCours($user->id);
        
        if ($pointageEnCours) {
            return redirect()->route('pointages.index')
                ->with('error', 'Vous avez déjà un pointage en cours.');
        }
        
        // Créer un nouveau pointage
        $pointage = new Pointage();
        $pointage->user_id = $user->id;
        $pointage->heure_entree = now();
        $pointage->statut = 'en_cours';
        $pointage->save();
        
        // Stocker l'ID du pointage en cours dans la session
        session(['pointage_en_cours_id' => $pointage->id]);
        
        return redirect()->route('pointages.index')
            ->with('success', 'Pointage d\'entrée enregistré avec succès à ' . $pointage->heure_entree->format('H:i:s'));
    }
    
    /**
     * Enregistre la sortie d'un pointage
     */
    public function sortie(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer le pointage en cours
        $pointage = Pointage::getPointageEnCours($user->id);
        
        if (!$pointage) {
            return redirect()->route('pointages.index')
                ->with('error', 'Aucun pointage en cours trouvé.');
        }
        
        // Enregistrer l'heure de sortie
        $pointage->heure_sortie = now();
        $pointage->calculerDuree();
        $pointage->save();
        
        // Supprimer l'ID du pointage en cours de la session
        session()->forget('pointage_en_cours_id');
        
        return redirect()->route('pointages.index')
            ->with('success', 'Pointage de sortie enregistré avec succès à ' . $pointage->heure_sortie->format('H:i:s') . 
                '. Durée totale : ' . $pointage->duree_formattee);
    }
    
    /**
     * Marque un pointage comme incomplet (en cas de déconnexion sans badger la sortie)
     */
    public function marquerIncomplet(Request $request, $id)
    {
        // Vérifier les permissions
        $user = Auth::user();
        if (!in_array($user->statut, ['admin', 'manager', 'gerant', 'co-gerant'])) {
            return redirect()->route('pointages.index')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour effectuer cette action.');
        }
        
        $pointage = Pointage::findOrFail($id);
        
        // Marquer comme incomplet avec un commentaire
        $commentaire = $request->input('commentaire', 'Pointage incomplet - Sortie non enregistrée');
        $pointage->marquerIncomplet($commentaire);
        
        return redirect()->route('pointages.index')
            ->with('success', 'Le pointage a été marqué comme incomplet.');
    }
    
    /**
     * Corrige un pointage (pour les managers)
     */
    public function corriger(Request $request, $id)
    {
        // Vérifier les permissions
        $user = Auth::user();
        if (!in_array($user->statut, ['admin', 'manager', 'gerant', 'co-gerant'])) {
            return redirect()->route('pointages.index')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour effectuer cette action.');
        }
        
        // Valider les données
        $validated = $request->validate([
            'heure_entree' => 'required|date',
            'heure_sortie' => 'required|date|after:heure_entree',
            'commentaire' => 'nullable|string|max:255',
        ]);
        
        $pointage = Pointage::findOrFail($id);
        
        // Mettre à jour le pointage
        $pointage->heure_entree = $validated['heure_entree'];
        $pointage->heure_sortie = $validated['heure_sortie'];
        $pointage->commentaire = $validated['commentaire'];
        $pointage->statut = 'termine';
        $pointage->calculerDuree();
        $pointage->save();
        
        return redirect()->route('pointages.index')
            ->with('success', 'Le pointage a été corrigé avec succès.');
    }
    
    /**
     * Affiche le formulaire de correction d'un pointage
     */
    public function edit($id): View
    {
        // Vérifier les permissions
        $user = Auth::user();
        if (!in_array($user->statut, ['admin', 'manager', 'gerant', 'co-gerant'])) {
            return redirect()->route('pointages.index')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour effectuer cette action.');
        }
        
        $pointage = Pointage::findOrFail($id);
        
        return view('pointages.edit', compact('pointage'));
    }
    
    /**
     * Traite la déconnexion automatique (appelé par AJAX)
     */
    public function deconnexionAuto(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié']);
        }
        
        // Récupérer le pointage en cours
        $pointage = Pointage::getPointageEnCours($user->id);
        
        if (!$pointage) {
            return response()->json(['success' => false, 'message' => 'Aucun pointage en cours trouvé']);
        }
        
        // Marquer comme incomplet
        $pointage->marquerIncomplet('Déconnexion automatique - Utilisateur a quitté la page');
        
        return response()->json(['success' => true, 'message' => 'Pointage marqué comme incomplet']);
    }
    
    /**
     * Récupère les statistiques de présence pour un employé (appelé par AJAX)
     */
    public function getStatsEmploye(Request $request, $id)
    {
        // Vérifier les permissions
        $user = Auth::user();
        if (!in_array($user->statut, ['admin', 'manager', 'gerant', 'co-gerant'])) {
            return response()->json(['success' => false, 'message' => 'Permissions insuffisantes']);
        }
        
        $employe = User::findOrFail($id);
        
        // Récupérer la semaine sélectionnée
        $week = $request->input('week', Carbon::now()->format('Y-\WW'));
        
        try {
            list($year, $weekNumber) = explode('-W', $week);
            $startOfWeek = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
            $endOfWeek = Carbon::now()->setISODate($year, $weekNumber)->endOfWeek();
        } catch (\Exception $e) {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
        }
        
        // Récupérer les pointages de l'employé pour la semaine
        $pointages = Pointage::getPointagesSemaine($employe->id, $startOfWeek, $endOfWeek);
        
        // Calculer le temps total de présence
        $tempsPresence = Pointage::getTempsPresenceSemaine($employe->id, $startOfWeek, $endOfWeek);
        
        // Formater le temps de présence
        $heures = floor($tempsPresence / 60);
        $minutes = $tempsPresence % 60;
        $tempsFormatte = sprintf('%02dh%02d', $heures, $minutes);
        
        // Préparer les données pour chaque jour de la semaine
        $joursSemaine = [];
        $pointagesParJour = [];
        
        for ($i = 0; $i < 7; $i++) {
            $jour = $startOfWeek->copy()->addDays($i);
            $joursSemaine[] = $jour->format('d/m');
            
            $pointagesJour = $pointages->filter(function($pointage) use ($jour) {
                return $pointage->heure_entree->format('Y-m-d') === $jour->format('Y-m-d');
            });
            
            $tempsJour = $pointagesJour->sum('duree_minutes');
            $heuresJour = floor($tempsJour / 60);
            $minutesJour = $tempsJour % 60;
            
            $pointagesParJour[] = [
                'date' => $jour->format('Y-m-d'),
                'jour' => $jour->format('d/m'),
                'jour_semaine' => $jour->translatedFormat('l'),
                'temps_minutes' => $tempsJour,
                'temps_formatte' => sprintf('%02dh%02d', $heuresJour, $minutesJour),
                'pointages' => $pointagesJour->map(function($p) {
                    return [
                        'id' => $p->id,
                        'heure_entree' => $p->heure_entree->format('H:i'),
                        'heure_sortie' => $p->heure_sortie ? $p->heure_sortie->format('H:i') : null,
                        'duree' => $p->duree_formattee,
                        'statut' => $p->statut
                    ];
                })
            ];
        }
        
        return response()->json([
            'success' => true,
            'employe' => [
                'id' => $employe->id,
                'nom' => $employe->nom,
                'prenom' => $employe->prenom,
                'statut' => $employe->statut
            ],
            'temps_total' => $tempsPresence,
            'temps_formatte' => $tempsFormatte,
            'jours' => $pointagesParJour
        ]);
    }
}
