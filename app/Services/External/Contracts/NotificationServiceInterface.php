<?php

namespace App\Services\External\Contracts;

interface NotificationServiceInterface
{
    public function notify(int $userId): bool;
}
