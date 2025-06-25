<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectDojUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Si l'utilisateur est doj, rediriger vers la page des salaires sauf si déjà sur cette page
        if (auth()->check() && auth()->user()->statut === 'doj' && !$request->routeIs('salaires.index')) {
            return redirect()->route('salaires.index');
        }

        return $next($request);
    }
}
