<?php

namespace App\Exceptions;

use App\Services\Error\ErrorFormatterService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

/**
 * Handler Global des Exceptions
 * Gère le rendu personnalisé des erreurs de l'application
 */
class Handler
{
    /**
     * Liste des exceptions qui ne doivent pas être signalées
     */
    protected array $dontReport = [
        //
    ];

    /**
     * Rendre une exception sous forme de réponse HTTP
     */
    public function renderException(Throwable $exception): Response
    {
        $statusCode = $this->getStatusCode($exception);
        $formatter = new ErrorFormatterService();
        $error = $formatter->format($statusCode, $exception);

        return response()->view('errors.error', compact('error'), $statusCode);
    }

    /**
     * Obtenir le code de statut HTTP approprié d'une exception
     */
    public function getStatusCode(Throwable $exception): int
    {
        // Exceptions de base de données
        if ($exception instanceof \Illuminate\Database\QueryException) {
            return 500;
        }

        // Exceptions Eloquent
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return 404;
        }

        // Authentification
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return 401;
        }

        // Autorisation
        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return 403;
        }

        // Validation
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return 422;
        }

        // Méthode HTTP non autorisée
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            return 405;
        }

        // Page non trouvée
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return 404;
        }

        // Exception HTTP générique Symfony
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            return $exception->getStatusCode();
        }

        // Exceptions Filament
        if (class_exists('Filament\\Exceptions\\Halt')) {
            if ($exception instanceof \Filament\Exceptions\Halt) {
                return 403;
            }
        }

        // Par défaut, 500 pour les exceptions non gérées
        return 500;
    }
}

