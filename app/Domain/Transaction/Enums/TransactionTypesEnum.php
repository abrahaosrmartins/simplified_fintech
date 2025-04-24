<?php

namespace App\Domain\Transaction\Enums;

enum TransactionTypesEnum: string
{
    case TRANSFER = 'transfer';
    case DEPOSIT = 'deposit';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
