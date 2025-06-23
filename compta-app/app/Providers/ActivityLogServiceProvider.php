<?php

namespace App\Providers;

use App\Services\ActivityLogger;
use Illuminate\Support\ServiceProvider;

class ActivityLogServiceProvider extends ServiceProvider
{
    /**
     * Enregistre les services de l'application.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('activity', function ($app) {
            return new ActivityLogger();
        });
    }

    /**
     * Bootstrap les services de l'application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
