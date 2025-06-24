<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paramètres de session personnalisés
    |--------------------------------------------------------------------------
    |
    | Ces paramètres sont utilisés pour résoudre les problèmes de session
    | et d'erreur CSRF 419 dans l'application.
    |
    */
    
    // Augmenter la durée de vie du cookie de session
    'lifetime' => 120, // 2 heures en minutes
    
    // Ne pas expirer la session à la fermeture du navigateur
    'expire_on_close' => false,
    
    // Paramètres de cookie sécurisés
    'secure' => false, // Mettre à true en production avec HTTPS
    'http_only' => true,
    'same_site' => 'lax',
    
    // Domaine du cookie (null = domaine actuel)
    'domain' => null,
    
    // Chemin du cookie
    'path' => '/',
];
