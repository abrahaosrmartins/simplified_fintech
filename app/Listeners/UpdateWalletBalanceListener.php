<?php

namespace App\Listeners;

use App\Domain\Events\TransactionApproved;
use App\Domain\Events\TransactionStatusUpdated;
use App\Domain\Transaction\Enums\TransactionStatusEnum;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateWalletBalanceListener implements ShouldQueue
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
    public function handle(TransactionStatusUpdated $event)
    {
        $transaction = $event->transaction;

        if (!$transaction->status === TransactionStatusEnum::PENDING) {
            return false;
        }

        $payerWallet = $this->walletRepository->findByUserIdLockedForUpdate($transaction->payer);
        $payeeWallet = $this->walletRepository->findByUserIdLockedForUpdate($transaction->payee);

        if (!$payeeWallet || !$payeeWallet) {
            $transaction->delete();
            return false;
        }

        $payerWallet->balance = bcsub($payerWallet->balance, $transaction->value);
        $payeeWallet->balance = bcadd($transaction->value, $transaction->value);
        $transaction->status = TransactionStatusEnum::APPROVED;

        $transaction->save();
        $payerWallet->save();
        $payeeWallet->save();
        event(new TransactionApproved($transaction));
    }
}
