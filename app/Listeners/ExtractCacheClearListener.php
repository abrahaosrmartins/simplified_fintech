<?php

namespace App\Listeners;

use App\Domain\Transaction\Enums\TransactionStatusEnum;
use Illuminate\Support\Facades\Cache;

class ExtractCacheClearListener
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $transaction = $event->transaction;
        if ($transaction->status === TransactionStatusEnum::APPROVED) {
            Cache::forget("user_extract_{$transaction->payerUser->id}_" . now()->format('Y_m'));
        }
    }
}
