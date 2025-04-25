<?php

namespace App\Providers;

use App\Domain\Transaction\Infrastructure\TransactionRepository;
use App\Domain\Transaction\Repositories\TransactionRepositoryInterface;
use App\Domain\Wallet\Infrastructure\WalletRepository;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use App\Services\External\AuthorizerService;
use App\Services\External\Contracts\AuthorizerServiceInterface;
use App\Services\External\Contracts\NotificationServiceInterface;
use App\Services\External\NotificationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);

        //externals
        $this->app->bind(AuthorizerServiceInterface::class, AuthorizerService::class);
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class);

        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
