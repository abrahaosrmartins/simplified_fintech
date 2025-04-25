<?php

namespace Tests\Unit;

use App\Application\Transaction\UseCases\CreateTransactionUseCase;
use App\Application\Transaction\UseCases\Dto\TransactionInputDto;
use App\Domain\Events\TransactionStatusUpdated;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Repositories\TransactionRepositoryInterface;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use App\Services\External\Contracts\AuthorizerServiceInterface;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class CreateTransactionUseCaseTest extends TestCase
{
    private $authorizeService;
    private $transactionRepository;
    private $walletRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizeService = Mockery::mock(AuthorizerServiceInterface::class);
        $this->transactionRepository = Mockery::mock(TransactionRepositoryInterface::class);
        $this->walletRepository = Mockery::mock(WalletRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_common_user_can_transfer_to_merchant()
    {
        Event::fake();
        $useCase = new CreateTransactionUseCase(
            $this->authorizeService,
            $this->transactionRepository,
            $this->walletRepository
        );

        $payer = new User(['type' => UserTypeEnum::COMMON]);
        $payer->setAttribute('id', 1);

        $payee = new User(['type' => UserTypeEnum::MERCHANT]);
        $payee->setAttribute('id', 2);

        $dto = new TransactionInputDto(
            50.00,
            $payer->id,
            $payee->id,
            'transfer'
        );

        $wallet = new Wallet(['user_id' => $payer->id, 'balance' => 100.00]);

        $this->walletRepository
            ->shouldReceive('findByUserId')
            ->once()
            ->with($payer->id)
            ->andReturn($wallet);

        $this->authorizeService
            ->shouldReceive('authorize')
            ->once()
            ->andReturn(true);

        $transaction = Mockery::mock(Transaction::class);
        $this->transactionRepository
            ->shouldReceive('store')
            ->once()
            ->with($dto)
            ->andReturn($transaction);

        $result = $useCase->execute($payer, $dto);

        $this->assertSame($transaction, $result);

        Event::assertDispatched(TransactionStatusUpdated::class);
    }

    public function test_common_user_can_transfer_to_another_common_user()
    {
        Event::fake();
        $useCase = new CreateTransactionUseCase(
            $this->authorizeService,
            $this->transactionRepository,
            $this->walletRepository
        );

        $payer = new User(['type' => UserTypeEnum::COMMON]);
        $payer->setAttribute('id', 1);

        $payee = new User(['type' => UserTypeEnum::COMMON]);
        $payee->setAttribute('id', 2);

        $dto = new TransactionInputDto(
            50.00,
            $payer->id,
            $payee->id,
            'transfer'
        );

        $wallet = new Wallet(['user_id' => $payer->id, 'balance' => 100.00]);

        $this->walletRepository
            ->shouldReceive('findByUserId')
            ->once()
            ->with($payer->id)
            ->andReturn($wallet);

        $this->authorizeService
            ->shouldReceive('authorize')
            ->once()
            ->andReturn(true);

        $transaction = Mockery::mock(Transaction::class);
        $this->transactionRepository
            ->shouldReceive('store')
            ->once()
            ->with($dto)
            ->andReturn($transaction);

        $result = $useCase->execute($payer, $dto);

        $this->assertSame($transaction, $result);

        Event::assertDispatched(TransactionStatusUpdated::class);
    }

    public function test_user_merchant_cannot_transfer()
    {
        $useCase = new CreateTransactionUseCase(
            $this->authorizeService,
            $this->transactionRepository,
            $this->walletRepository
        );

        $merchantUser = new User(['type' => UserTypeEnum::MERCHANT]);
        $merchantUser->setAttribute('id', 1);

        $commonUser = new User(['type' => UserTypeEnum::COMMON]);
        $commonUser->setAttribute('id', 2);

        $dto = new TransactionInputDto(
            100.00,
            $merchantUser->id,
            $commonUser->id,
            'transfer'
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Oops! Usuários lojistas não podem fazer transferências.');

        $useCase->execute($merchantUser, $dto);
    }

    public function test_user_cannot_transfer_to_themself()
    {
        $useCase = new CreateTransactionUseCase(
            $this->authorizeService,
            $this->transactionRepository,
            $this->walletRepository
        );

        $commonUser = new User(['type' => UserTypeEnum::COMMON]);
        $commonUser->setAttribute('id', 2);

        $dto = new TransactionInputDto(
            100.00,
            $commonUser->id,
            $commonUser->id,
            'transfer'
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Oops! Você não pode transferir saldo para você mesmo!');

        $useCase->execute($commonUser, $dto);
    }

    public function test_user_cannot_transfer_without_balance()
    {
        $useCase = new CreateTransactionUseCase(
            $this->authorizeService,
            $this->transactionRepository,
            $this->walletRepository
        );

        $payer = new User(['type' => UserTypeEnum::COMMON]);
        $payer->setAttribute('id', 1);

        $payee = new User(['type' => UserTypeEnum::COMMON]);
        $payee->setAttribute('id', 2);

        $wallet = new Wallet(['user_id' => $payer->id, 'balance' => 100.00]);

        $this->walletRepository
            ->shouldReceive('findByUserId')
            ->once()
            ->with($payer->id)
            ->andReturn($wallet);

        $dto = new TransactionInputDto(
            200.00,
            $payer->id,
            $payee->id,
            'transfer'
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Oops! Você não possui saldo suficiente. :(');

        $useCase->execute($payer, $dto);
    }

    public function test_user_cannot_transfer_using_another_persons_id()
    {
        $useCase = new CreateTransactionUseCase(
            $this->authorizeService,
            $this->transactionRepository,
            $this->walletRepository
        );

        $userAuthenticated = new User(['type' => UserTypeEnum::COMMON]);
        $userAuthenticated->setAttribute('id', 1);

        $merchantUser = new User(['type' => UserTypeEnum::COMMON]);
        $merchantUser->setAttribute('id', 2);

        $commonUser = new User(['type' => UserTypeEnum::COMMON]);
        $commonUser->setAttribute('id', 3);

        $dto = new TransactionInputDto(
            100.00,
            $merchantUser->id,
            $commonUser->id,
            'transfer'
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Oops! Não é possível realizar essa transação!');

        $useCase->execute($userAuthenticated, $dto);
    }
}
