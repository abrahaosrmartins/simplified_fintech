<?php

namespace App\Services\External\Contracts;

interface AuthorizerServiceInterface
{
    public function authorize(): bool;
}
