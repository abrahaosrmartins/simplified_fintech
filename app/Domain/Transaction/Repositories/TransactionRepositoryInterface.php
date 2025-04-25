<?php

namespace App\Domain\Transaction\Repositories;

use App\Application\Transaction\UseCases\Dto\TransactionInputDto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface TransactionRepositoryInterface
{
    public function store(TransactionInputDto $inputDto): ?Model;
    public function findByIdAndPayer(int $transactionId, int $payerId): ?Model;
    public function getSentTransactionsByUserId(int $userId): Collection;
}
