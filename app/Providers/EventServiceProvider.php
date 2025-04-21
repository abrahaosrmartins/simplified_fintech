<?php

namespace App\Providers;

use App\Domain\TransactionWasApproved;
use App\Listeners\UpdateWalletBalanceListener;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TransactionWasApproved::class => [
            UpdateWalletBalanceListener::class,
        ],
    ];

}
