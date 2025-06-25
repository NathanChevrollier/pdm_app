<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictDojAccess
{
    /**
     * Restreint l'accès des utilisateurs avec le statut "doj" uniquement à la page des salaires.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Si l'utilisateur a le statut "doj"
        if ($user->statut === 'doj') {
            // Si la route n'est pas salaires.index, rediriger vers salaires.index
            if (!$request->routeIs('salaires.index')) {
                return redirect()->route('salaires.index');
            }
        }

        return $next($request);
    }
}
