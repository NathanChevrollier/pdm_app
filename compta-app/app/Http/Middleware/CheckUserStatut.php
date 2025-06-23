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
            // Convertir les anciens statuts (Patron, Co-patron) vers les nouveaux (admin, employe) si nécessaire
            $mappedStatuts = array_map(function($statut) {
                switch($statut) {
                    case 'Patron':
                    case 'Co-patron':
                    case 'Manager':
                        return 'admin';
                    case 'Vendeur':
                    case 'Recrue':
                        return 'employe';
                    default:
                        return $statut;
                }
            }, $statuts);
            
            if (!in_array($user->statut, $mappedStatuts)) {
                return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
            }
        }

        return $next($request);
    }
}
