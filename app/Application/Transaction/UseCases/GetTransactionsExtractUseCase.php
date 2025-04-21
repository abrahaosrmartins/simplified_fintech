<?php

namespace App\Application\Transaction\UseCases;

use App\Application\Transaction\UseCases\Dto\PaginationParamsDto;
use App\Domain\Transaction\Repositories\TransactionRepositoryInterface;
use App\Domain\User\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class GetTransactionsExtractUseCase
{
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(User $user, PaginationParamsDto $paginationParamsDto)
    {
        $cacheKey = "user_extract_{$user->id}_" . now()->format('Y_m');

        $transactions = Cache::remember($cacheKey, now()->addHours(24), function () use ($user) {
            return $this->transactionRepository->getSentTransactionsByUserId($user->id);
        });

        $page = $paginationParamsDto->page;

        return new LengthAwarePaginator(
            $transactions->forPage($page, $paginationParamsDto->perPage),
            $transactions->count(),
            $paginationParamsDto->perPage,
            $page,
            ['path' => $paginationParamsDto->path, 'query' => $paginationParamsDto->query]
        );
    }
}
