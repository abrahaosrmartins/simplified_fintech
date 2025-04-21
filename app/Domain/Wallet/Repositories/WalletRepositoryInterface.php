<?php

namespace App\Domain\Wallet\Repositories;

use Illuminate\Database\Eloquent\Model;

interface WalletRepositoryInterface
{
    public function findById(int $walletId): Model;
    public function findByUserId(int $userId): Model;
    public function findByUserIdLockedForUpdate(int $userId): Model;
}
