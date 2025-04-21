<?php

namespace App\Domain\Transaction\Repositories;

use App\Application\Transaction\UseCases\Dto\TransactionInputDto;
use Illuminate\Database\Eloquent\Model;

interface TransactionRepositoryInterface
{
    public function store(TransactionInputDto $transactionInputDto): Model;
    public function findByIdAndPayer(int $transactionId, int $payerId): Model;
}
