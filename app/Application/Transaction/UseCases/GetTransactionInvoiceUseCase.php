<?php

namespace App\Application\Transaction\UseCases;

use App\Domain\Transaction\Repositories\TransactionRepositoryInterface;
use App\Domain\User\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;

class GetTransactionInvoiceUseCase
{
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(User $user, int $transactionId)
    {
        $cacheKey = "transaction_invoice_{$user->id}_{$transactionId}";

        $invoice = Cache::remember($cacheKey, now()->addHours(24), function () use ($transactionId, $user) {
            return $this->transactionRepository->findByIdAndPayer($transactionId, $user->id);
        });

        if (!$invoice) {
            throw new Exception('Oops! Não foi possível realizar essa ação. Por favor, contate a administração do sistema.');
        }

        return [
            'payee' => $invoice->payeeUser->name,
            'value' => $invoice->value,
            'date' => $invoice->created_at->toDateTimeString()
        ];
    }
}
