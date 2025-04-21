<?php

namespace App\Domain\Wallet\Infrastructure;

use App\Domain\Wallet\Models\Wallet;
use Illuminate\Database\Eloquent\Model;

class WalletRepository
{
    private $model;

    public function __construct()
    {
        $this->model = new Wallet();
    }

    public function findById(int $walletId): Model
    {
        return $this->model->findById($walletId);
    }

    public function findByUserId(int $userId): Model
    {
        return $this->model->where('user_id', $userId);
    }
}
