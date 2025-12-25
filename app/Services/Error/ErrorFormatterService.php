<?php

namespace App\Services\Error;

use Illuminate\Http\Response;
use Throwable;

/**
 * Service de formatage et gestion des erreurs
 * Responsable de formater les erreurs pour l'affichage
 */
class ErrorFormatterService
{
    /**
     * Messages d'erreur personnalisés par code HTTP
     * Utilise la configuration si disponible, sinon les valeurs par défaut
     */
    private array $errorMessages = [];

    public function __construct()
    {
        // Charger la configuration personnalisée si disponible
        $this->errorMessages = config('errors.messages', $this->getDefaultMessages());
    }

    /**
     * Obtenir les messages d'erreur par défaut
     */
    private function getDefaultMessages(): array
    {
        return [
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
        ];
    }

    /**
     * Formater une erreur pour l'affichage
     */
    public function format(int $statusCode, Throwable|null $exception = null): array
    {
        $defaultMessage = $this->errorMessages[$statusCode] ?? $this->getDefaultMessage($statusCode);

        return [
            'status' => $statusCode,
            'title' => $defaultMessage['title'],
            'description' => $defaultMessage['description'],
            'icon' => $defaultMessage['icon'],
            'message' => $exception?->getMessage(),
            'debug' => $this->getDebugInfo($exception),
        ];
    }

    /**
     * Obtenir les informations de débogage (en dev uniquement)
     */
    private function getDebugInfo(Throwable|null $exception): array|null
    {
        if (!config('app.debug') || !$exception) {
            return null;
        }

        return [
            'message' => $exception->getMessage(),
            'file' => str_replace(base_path(), '', $exception->getFile()),
            'line' => $exception->getLine(),
            'trace' => collect($exception->getTrace())
                ->take(5)
                ->map(fn($item) => [
                    'file' => isset($item['file']) ? str_replace(base_path(), '', $item['file']) : 'Unknown',
                    'line' => $item['line'] ?? 'Unknown',
                    'function' => $item['function'] ?? 'Unknown',
                    'class' => $item['class'] ?? null,
                ])
                ->toArray(),
        ];
    }

    /**
     * Message par défaut pour les codes d'erreur non gérés
     */
    private function getDefaultMessage(int $statusCode): array
    {
        if ($statusCode >= 500) {
            return [
                'title' => 'Erreur Serveur',
                'description' => 'Une erreur serveur s\'est produite. Veuillez réessayer plus tard.',
                'icon' => '⚠️'
            ];
        }

        if ($statusCode >= 400) {
            return [
                'title' => 'Erreur Client',
                'description' => 'Une erreur s\'est produite avec votre requête.',
                'icon' => '⚠️'
            ];
        }

        return [
            'title' => 'Erreur',
            'description' => 'Une erreur s\'est produite.',
            'icon' => '⚠️'
        ];
    }
}
