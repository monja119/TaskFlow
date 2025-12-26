<?php

namespace App\Services;

use App\Enums\ProjectStatus;

/**
 * Service pour formater l'affichage du statut d'un projet
 * 
 * la logique de formatage du statut au même endroit.
 */
class ProjectStatusFormatter
{
    /**
     * Formate le statut d'un projet en français
     *
     * @param mixed $status La valeur du statut (string, int, ou ProjectStatus enum)
     * @return string Le libellé du statut en français
     */
    public static function format(mixed $status): string
    {
        if ($status instanceof ProjectStatus) {
            return $status->getLabel();
        }

        return ProjectStatus::from($status)->getLabel();
    }
}
