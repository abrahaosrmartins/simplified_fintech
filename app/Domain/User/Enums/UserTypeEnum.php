<?php

namespace App\Domain\User\Enums;

enum UserTypeEnum: string
{
    case COMMON = 'common';
    case MERCHANT = 'merchant';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
