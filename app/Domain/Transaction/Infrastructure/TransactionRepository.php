<?php

namespace App\Domain\Transaction\Infrastructure;

use App\Domain\Transaction\Models\Transaction;

class TransactionRepository
{
    private $model;

    public function __construct()
    {
        $this->model = new Transaction();
    }

    public function store()
    {
        $data = [

        ];
        return $this->model->create($data);
    }
}
