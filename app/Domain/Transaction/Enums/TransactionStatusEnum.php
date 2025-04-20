<?php

namespace App\Domain\Transaction\Enums;

enum TransactionStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case FAILED = 'failed';
}
