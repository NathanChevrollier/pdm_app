<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paramètres de l'application
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient les paramètres de configuration personnalisés pour
    | l'application de gestion commerciale.
    |
    */


    /*
    |--------------------------------------------------------------------------
    | Paramètres de l'application
    |--------------------------------------------------------------------------
    */
    'app' => [
        'name' => 'Gestion Commerciale',
        'version' => '1.0.0',
        'logo' => 'sneat-1.0.0/assets/img/logo.png',
        'favicon' => 'sneat-1.0.0/assets/img/favicon/favicon.ico',
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres de l'entreprise
    |--------------------------------------------------------------------------
    */
    'company' => [
        'name' => 'Votre Entreprise',
        'address' => '123 Rue de l\'Exemple',
        'city' => 'Abidjan',
        'country' => 'Côte d\'Ivoire',
        'phone' => '+225 XX XX XX XX',
        'email' => 'contact@example.com',
        'website' => 'www.example.com',
        'rc' => 'CI-ABJ-XX-XXXXX',
        'contribuable' => 'XXXXXX',
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres des devises
    |--------------------------------------------------------------------------
    */
    'currency' => [
        'code' => 'XOF',
        'symbol' => 'FCFA',
        'thousand_separator' => ' ',
        'decimal_separator' => ',',
        'decimal_digits' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres des documents
    |--------------------------------------------------------------------------
    */
    'documents' => [
        'path' => 'documents',
        'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'],
        'max_size' => 10240, // en Ko (10 Mo)
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres des sauvegardes
    |--------------------------------------------------------------------------
    */
    'backup' => [
        'path' => 'backups',
        'keep_days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres des notifications
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'email' => [
            'enabled' => true,
            'from_address' => 'noreply@example.com',
            'from_name' => 'Gestion Commerciale',
        ],
        'sms' => [
            'enabled' => false,
            'provider' => 'twilio', // ou autre fournisseur
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres des rapports
    |--------------------------------------------------------------------------
    */
    'reports' => [
        'default_date_range' => 'this_month', // today, yesterday, this_week, last_week, this_month, last_month, this_year, last_year, custom
        'date_format' => 'd/m/Y',
        'time_format' => 'H:i',
        'timezone' => 'Africa/Abidjan',
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres des exports
    |--------------------------------------------------------------------------
    */
    'exports' => [
        'path' => 'exports',
        'keep_days' => 7,
        'chunk_size' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres de l'interface utilisateur
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'theme' => 'light', // light, dark, system
        'sidebar' => [
            'collapsed' => false,
            'mini' => false,
            'fixed' => true,
        ],
        'navbar' => [
            'fixed' => true,
        ],
        'footer' => [
            'fixed' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres des fonctionnalités
    |--------------------------------------------------------------------------
    */
    'features' => [
        'vehicles' => [
            'enabled' => true,
            'stock_management' => true,
            'images' => true,
            'documents' => true,
        ],
        'employees' => [
            'enabled' => true,
            'commissions' => true,
            'documents' => true,
        ],
        'orders' => [
            'enabled' => true,
            'invoices' => true,
            'payments' => true,
            'documents' => true,
        ],
        'salaries' => [
            'enabled' => true,
            'auto_calculate' => true,
            'payslips' => true,
        ],
    ],
];
