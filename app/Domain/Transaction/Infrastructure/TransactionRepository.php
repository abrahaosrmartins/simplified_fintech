<?php

namespace App\Domain\Transaction\Infrastructure;

use App\Application\Transaction\UseCases\Dto\TransactionInputDto;
use App\Domain\Transaction\Enums\TransactionStatusEnum;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Repositories\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TransactionRepository implements TransactionRepositoryInterface
{
    private $model;

    public function __construct()
    {
        $this->model = new Transaction();
    }

    public function store(TransactionInputDto $transactionInputDto): Model
    {
        $data = [
            'payer' => $transactionInputDto->payer,
            'payee' => $transactionInputDto->payee,
            'value' => $transactionInputDto->value,
            'status' => TransactionStatusEnum::APPROVED,
        ];

        return $this->model->create($data);
    }

    public function findByIdAndPayer(int $transactionId, int $payerId): Model
    {
        return $this->model
            ->with('payeeUser')
            ->where('payer', $payerId)
            ->where('id', $transactionId)
            ->first();
    }

    public function getSentTransactionsByUserId(int $userId): Collection
    {
        return $this->model
            ->where('payer', $userId)
            ->where('created_at', '>=', now()->subMonth())
            ->orderByDesc('created_at')
            ->get();
    }
}
