<?php

namespace App\Domain\User\Enums;

enum DocumentTypeEnum: string
{
    case CPF = 'cpf';
    case CNPJ = 'cnpj';
}
