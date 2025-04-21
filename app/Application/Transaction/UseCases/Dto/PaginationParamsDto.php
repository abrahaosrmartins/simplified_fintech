<?php

namespace App\Application\Transaction\UseCases\Dto;

class PaginationParamsDto
{
    public int $perPage;
    public int $page;
    public string $path;
    public array $query;

    public function __construct(
        int $perPage,
        int $page,
        string $path,
        array $query
    ) {
        $this->perPage = $perPage;
        $this->page = $page;
        $this->path = $path;
        $this->query = $query;
    }
}
