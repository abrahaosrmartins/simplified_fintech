<?php

namespace App\Providers;

use App\Domain\Events\TransactionApproved;
use App\Domain\Events\TransactionStatusUpdated;
use App\Listeners\ExtractCacheClearListener;
use App\Listeners\NotifyPayeeListener;
use App\Listeners\UpdateWalletBalanceListener;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TransactionStatusUpdated::class => [
            UpdateWalletBalanceListener::class,
        ],

        TransactionApproved::class => [
            NotifyPayeeListener::class,
            ExtractCacheClearListener::class,
        ],
    ];

}
