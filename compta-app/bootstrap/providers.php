<?php

return [
    // Fournisseurs de services de base
    App\Providers\AppServiceProvider::class,
    
    // Fournisseurs d'événements
    App\Providers\EventServiceProvider::class,
    
    // Fournisseurs personnalisés
    App\Providers\ActivityLogServiceProvider::class,
    App\Providers\SessionServiceProvider::class, // Gestion des sessions personnalisées
];