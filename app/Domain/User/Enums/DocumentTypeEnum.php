<?php

namespace App\Domain\User\Enums;

enum DocumentTypeEnum: string
{
    case CPF = 'cpf';
    case CNPJ = 'cnpj';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
