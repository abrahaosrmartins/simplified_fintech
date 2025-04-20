<?php

namespace App\Domain\Transaction\Enums;

enum TransactionStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case FAILED = 'failed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
