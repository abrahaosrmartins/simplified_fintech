<?php

namespace App\Domain\Events;

use App\Domain\Transaction\Models\Transaction;

class TransactionStatusUpdated
{
    public Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
