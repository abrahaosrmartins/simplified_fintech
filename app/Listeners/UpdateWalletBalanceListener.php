<?php

namespace App\Listeners;

use App\Domain\Transaction\Enums\TransactionStatusEnum;
use App\Domain\TransactionStatusUpdated;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use Exception;

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
    public function handle(TransactionStatusUpdated $event): void
    {
        $transaction = $event->transaction;

        if (!$transaction->status === TransactionStatusEnum::PENDING) {
            return;
        }

        $payerWallet = $this->walletRepository->findByUserIdLockedForUpdate($transaction->payer);
        $payeeWallet = $this->walletRepository->findByUserIdLockedForUpdate($transaction->payee);

        if (!$payeeWallet || !$payeeWallet) {
            throw new Exception('Oops! Não conseguimos completar essa transação no momento. Por favor. Contate a administração do sistema.');
        }

        $payerWallet->balance -= $transaction->value;
        $payeeWallet->balance += $transaction->value;
        $transaction->status = TransactionStatusEnum::APPROVED;

        $transaction->save();
        $payerWallet->save();
        $payeeWallet->save();
    }
}
