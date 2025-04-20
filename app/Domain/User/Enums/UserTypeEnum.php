<?php

namespace App\Domain\User\Enums;

enum UserTypeEnum: string
{
    case COMMON = 'common';
    case MERCHANT = 'merchant';
}
