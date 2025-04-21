<?php

namespace App\Application\Transaction\UseCases;

use App\Application\Transaction\UseCases\Dto\TransactionInputDto;
use App\Domain\Transaction\Repositories\TransactionRepositoryInterface;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use App\Services\External\Contracts\AuthorizerServiceInterface;
use App\Services\External\Contracts\NotificationServiceInterface;
use Exception;

class CreateTransactionUseCase
{
    private AuthorizerServiceInterface $authorizeService;
    private TransactionRepositoryInterface $transactionRepository;
    private NotificationServiceInterface $notificationService;
    private WalletRepositoryInterface $walletRepository;

    public function __construct(
        AuthorizerServiceInterface $authorizeService,
        NotificationServiceInterface $notificationService,
        TransactionRepositoryInterface $transactionRepository,
        WalletRepositoryInterface $walletRepository
    ) {
        $this->authorizeService = $authorizeService;
        $this->transactionRepository = $transactionRepository;
        $this->notificationService = $notificationService;
        $this->walletRepository = $walletRepository;
    }

    public function execute(User $user, TransactionInputDto $transactionInputDto)
    {
        $this->validatesUserCanMakeTransfer($user);
        $this->validatesUserHasBalance($user, $transactionInputDto->value);
        // $this->executesExternalValidation();
        $transaction = $this->persistTransaction($transactionInputDto);
        $this->notificationService->notify($user->id);

        return $transaction;
    }

    private function validatesUserCanMakeTransfer($user)
    {
        if ($user->type == UserTypeEnum::MERCHANT) {
            throw new Exception('Oops! Você não pode realizar essa ação!');
        }

        return true;
    }

    private function validatesUserHasBalance($user, $value) {
        $wallet = $this->walletRepository->findByUserId($user->id);

        if (!$wallet || $wallet->balance < $value) {
            throw new Exception('Você não possui saldo suficiente.');
        }

        return true;
    }

    private function executesExternalValidation()
    {
        if (!$this->authorizeService->authorize()) {
            throw new Exception('Oops! Você não pode realizar essa ação!');
        }

        return true;
    }

    private function persistTransaction($input)
    {
        $transaction = $this->transactionRepository->store($input);

        if (!$transaction) {
            throw new Exception('Oops! Não foi possível executar estar ação. Por favor tente mais tarde ou contacte o administrado do sistema');
        }

        return $transaction;
    }
}
