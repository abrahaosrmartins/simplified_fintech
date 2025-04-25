<?php

namespace App\Listeners;

use App\Domain\Events\TransactionApproved;
use App\Domain\Transaction\Enums\TransactionStatusEnum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;

class ExtractCacheClearListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(TransactionApproved $event): void
    {
        $transaction = $event->transaction;
        if ($transaction->status === TransactionStatusEnum::APPROVED) {
            Cache::forget("user_extract_{$transaction->payerUser->id}_" . now()->format('Y_m'));
        }
    }
}
