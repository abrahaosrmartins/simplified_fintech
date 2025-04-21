<?php

namespace App\Listeners;

use App\Domain\TransactionWasApproved;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateWalletBalanceListener
{
    private WalletRepositoryInterface $walletRepository;

    /**
     * Create the event listener.
     */
    public function __construct(WalletRepositoryInterface $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionWasApproved $event): void
    {
        $transaction = $event->transaction;

        $payerWallet = $this->walletRepository->findByUserIdLockedForUpdate($transaction->payer);
        $payeeWallet = $this->walletRepository->findByUserIdLockedForUpdate($transaction->payee);

        $payerWallet->balance -= $transaction->value;
        $payeeWallet->balance += $transaction->value;

        $payerWallet->save();
        $payeeWallet->save();
    }
}
