<?php

namespace App\Listeners;

use App\Domain\Transaction\Enums\TransactionStatusEnum;
use App\Domain\TransactionStatusUpdated;
use App\Services\External\Contracts\NotificationServiceInterface;

class NotifyPayeeListener
{

    private NotificationServiceInterface $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionStatusUpdated $event): void
    {
        $transaction = $event->transaction;
        if ($transaction->status === TransactionStatusEnum::APPROVED) {
            $this->notificationService->notify($transaction->payee);
        }
    }
}
