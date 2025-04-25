<?php

namespace App\Listeners;

use App\Domain\Events\TransactionApproved;
use App\Domain\Events\TransactionStatusUpdated;
use App\Domain\Transaction\Enums\TransactionStatusEnum;
use App\Services\External\Contracts\NotificationServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyPayeeListener implements ShouldQueue
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
    public function handle(TransactionApproved $event): void
    {
        $transaction = $event->transaction;
        if ($transaction->status === TransactionStatusEnum::APPROVED) {
            $this->notificationService->notify($transaction->payee);
        }
    }
}
