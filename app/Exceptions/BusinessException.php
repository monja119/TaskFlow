<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

/**
 * Exception métier personnalisée
 * À utiliser pour les erreurs spécifiques au domaine
 */
class BusinessException extends Exception
{
    protected $statusCode = 400;

    public function __construct(string $message = '', int $statusCode = 400)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
