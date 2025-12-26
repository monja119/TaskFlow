<?php

namespace App\Services\Notification;

use Illuminate\Database\Eloquent\Model;

interface NotificationServiceInterface
{
    /**
     * Send notification to appropriate recipients
     */
    public function send(Model $subject, array $context = []): void;
}
