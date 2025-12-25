<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Messages d'erreur personnalisés
    |--------------------------------------------------------------------------
    |
    | Définissez les messages affichés pour chaque code d'erreur HTTP.
    | Chaque entrée doit contenir un titre, une description et une icône.
    |
    */

    'messages' => [
        400 => [
            'title' => 'Requête Invalide',
            'description' => 'La requête envoyée est malformée ou invalide.',
            'icon' => '⚠️'
        ],
        401 => [
            'title' => 'Non Authentifié',
            'description' => 'Vous devez vous connecter pour accéder à cette ressource.',
            'icon' => '🔐'
        ],
        403 => [
            'title' => 'Accès Refusé',
            'description' => 'Vous n\'avez pas les permissions pour accéder à cette ressource.',
            'icon' => '🚫'
        ],
        404 => [
            'title' => 'Page Non Trouvée',
            'description' => 'La ressource que vous recherchez n\'existe pas ou a été supprimée.',
            'icon' => '🔍'
        ],
        405 => [
            'title' => 'Méthode Non Autorisée',
            'description' => 'La méthode HTTP utilisée n\'est pas autorisée pour cette ressource.',
            'icon' => '❌'
        ],
        408 => [
            'title' => 'Délai d\'Attente Dépassé',
            'description' => 'La requête a pris trop de temps. Veuillez réessayer.',
            'icon' => '⏱️'
        ],
        422 => [
            'title' => 'Données Invalides',
            'description' => 'Les données envoyées ne sont pas valides. Veuillez vérifier votre saisie.',
            'icon' => '📝'
        ],
        429 => [
            'title' => 'Trop de Requêtes',
            'description' => 'Vous avez fait trop de requêtes. Veuillez attendre avant de réessayer.',
            'icon' => '🚦'
        ],
        500 => [
            'title' => 'Erreur Interne du Serveur',
            'description' => 'Une erreur s\'est produite. Nos équipes ont été notifiées.',
            'icon' => '⚡'
        ],
        502 => [
            'title' => 'Mauvaise Passerelle',
            'description' => 'Le serveur a reçu une réponse invalide. Veuillez réessayer.',
            'icon' => '🔌'
        ],
        503 => [
            'title' => 'Service Indisponible',
            'description' => 'Le service est temporairement indisponible. Veuillez réessayer plus tard.',
            'icon' => '🔧'
        ],
        504 => [
            'title' => 'Délai d\'Attente Dépassé',
            'description' => 'Le serveur a mis trop de temps à répondre. Veuillez réessayer.',
            'icon' => '⏳'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Afficher les informations de débogage en production
    |--------------------------------------------------------------------------
    |
    | Par défaut, les informations de débogage ne sont affichées qu'en mode
    | développement (APP_DEBUG=true). Vous pouvez forcer l'affichage ici.
    |
    */

    'show_debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Redirection après erreur
    |--------------------------------------------------------------------------
    |
    | URL vers laquelle rediriger après une erreur non gérée.
    | Laissez null pour utiliser la page d'accueil par défaut.
    |
    */

    'redirect_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Rapport d'erreurs
    |--------------------------------------------------------------------------
    |
    | Configurez où et comment les erreurs doivent être reportées.
    |
    */

    'report' => [
        'enabled' => env('ERROR_REPORT_ENABLED', true),
        'channel' => 'single',  // 'single', 'slack', 'mail', etc.
    ],
];
