<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class SessionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Charger la configuration personnalisée des sessions
        $customConfig = Config::get('custom_session');
        
        if ($customConfig) {
            // Appliquer les paramètres personnalisés
            Config::set('session.lifetime', $customConfig['lifetime']);
            Config::set('session.expire_on_close', $customConfig['expire_on_close']);
            Config::set('session.secure', $customConfig['secure']);
            Config::set('session.http_only', $customConfig['http_only']);
            Config::set('session.same_site', $customConfig['same_site']);
            
            if ($customConfig['domain'] !== null) {
                Config::set('session.domain', $customConfig['domain']);
            }
            
            Config::set('session.path', $customConfig['path']);
        }
    }
}
