<?php

namespace App\Application\Transaction\UseCases;

use App\Application\Transaction\UseCases\Dto\TransactionInputDto;
use App\Domain\Transaction\Repositories\TransactionRepositoryInterface;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use Exception;

class CreateTransactionUseCase
{
    private TransactionRepositoryInterface $transactionRepositoryInterface;
    private WalletRepositoryInterface $walletRepository;

    public function __construct(
        TransactionRepositoryInterface $transactionRepositoryInterface,
        WalletRepositoryInterface $walletRepository
    ) {
        $this->transactionRepositoryInterface = $transactionRepositoryInterface;
        $this->walletRepository = $walletRepository;
    }

    public function execute(User $user, TransactionInputDto $transactionInputDto)
    {
        if ($user->type == UserTypeEnum::MERCHANT) {
            throw new Exception('Oops! Você não pode realizar essa ação!');
        }

        $wallet = $this->walletRepository->findByUserId($user->id);
        return true;
    }
}
