<?php

namespace App\Domain\Wallet\Infrastructure;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class WalletRepository implements WalletRepositoryInterface
{
    private $model;

    public function __construct()
    {
        $this->model = new Wallet();
    }

    public function findById(int $walletId): ?Model
    {
        return $this->model->find($walletId);
    }

    public function findByUserId(int $userId): ?Model
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function findByUserIdLockedForUpdate(int $userId): ?Model
    {
        return $this->model->where('user_id', $userId)->lockForUpdate()->first();
    }
}
