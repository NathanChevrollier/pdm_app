<?php

namespace App\Providers;

use App\Events\ActivityLogged;
use App\Listeners\LogActivity;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Les mappages d'écouteurs d'événements pour l'application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ActivityLogged::class => [
            LogActivity::class,
        ],
    ];
    
    /**
     * Enregistre les écouteurs d'événements pour l'application.
     */
    public function boot(): void
    {
        parent::boot();
    }
    
    /**
     * Enregistre les services de l'application.
     */
    public function register(): void
    {
        //
    }
}
