<?php

namespace App\Domain\Events;

use App\Domain\Transaction\Models\Transaction;

class TransactionApproved
{
    public Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
