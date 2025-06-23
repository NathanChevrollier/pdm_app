<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Fusionner la configuration personnalisée
        $this->mergeConfigFrom(
            base_path('config/app-settings.php'), 'app_settings'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Partager les variables globales avec toutes les vues
        View::share('appName', config('app.name', 'Gestion Commerciale'));
        View::share('appVersion', config('app_settings.app.version', '1.0.0'));
        
        // Partager les informations de l'entreprise avec toutes les vues
        View::share('company', config('app_settings.company'));
        
        // Partager les paramètres de devise avec toutes les vues
        View::share('currency', config('app_settings.currency'));
        
        // Définir la locale de l'application
        app()->setLocale(config('app.locale', 'fr'));
        
        // Définir le fuseau horaire de l'application
        date_default_timezone_set(config('app.timezone', 'Africa/Abidjan'));
    }
}
