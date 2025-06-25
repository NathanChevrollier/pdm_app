<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatut
{
    /**
     * Vérifie si l'utilisateur connecté a le statut requis.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  $statuts
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$statuts)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();
        
        // Vérifier si l'utilisateur a un statut
        if (!$user->statut) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas de statut défini dans le système.');
        }
        
        // Vérifier si l'utilisateur a le statut requis
        if (!empty($statuts)) {
            // Convertir les statuts pour la vérification
            $userStatut = strtolower($user->statut);
            
            // Normaliser les statuts pour la comparaison
            $normalizedStatuts = array_map('strtolower', $statuts);
            
            // Mappings spécifiques pour la rétrocompatibilité
            if ($userStatut === 'patron') $userStatut = 'admin';
            if ($userStatut === 'co-patron') $userStatut = 'co-gerant';
            if ($userStatut === 'recrue') $userStatut = 'stagiaire';
            
            if (!in_array($userStatut, $normalizedStatuts)) {
                return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
            }
        }

        return $next($request);
    }
}
