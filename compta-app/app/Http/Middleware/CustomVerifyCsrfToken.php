<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;

class CustomVerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // Aucune route n'est exclue par défaut
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        try {
            // Essayer de vérifier le token CSRF normalement
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            // Si l'erreur est due à une expiration de session, régénérer la session
            if ($request->is('login') || $request->is('*/login')) {
                // Régénérer la session
                $request->session()->regenerateToken();
                
                // Journaliser l'erreur pour le débogage
                Log::warning('Session expirée lors de la connexion. Session régénérée.');
                
                // Rediriger vers la page de connexion avec un message
                return redirect()->route('login')
                    ->with('status', 'Votre session a expiré. Veuillez réessayer.');
            }
            
            // Pour les autres routes, lancer l'exception normalement
            throw $e;
        }
    }
}
