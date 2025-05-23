<?php

namespace Tests\Feature;

use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use App\Services\External\Contracts\AuthorizerServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_common_user_can_transfer_to_merchant()
    {
        Event::fake();
        $payer = User::factory()->create([
            'type' => UserTypeEnum::COMMON,
        ]);
        $payee = User::factory()->create([
            'type' => UserTypeEnum::MERCHANT,
        ]);
        $token = $payer->createToken('auth_token')->plainTextToken;

        Wallet::factory()->create([
            'user_id' => $payer->id,
            'balance' => 1000,
        ]);

        $this->mock(AuthorizerServiceInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/transfer', [
                'payer' => $payer->id,
                'payee' => $payee->id,
                'value' => 100.50,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    "payer",
                    "payee",
                    "value",
                    "status",
                    "updated_at",
                    "created_at",
                    "id",
                ],
            ]);
    }

    public function test_common_user_can_transfer_to_another_common_user()
    {
        Event::fake();
        $payer = User::factory()->create([
            'type' => UserTypeEnum::COMMON,
        ]);
        $payee = User::factory()->create([
            'type' => UserTypeEnum::COMMON,
        ]);
        $token = $payer->createToken('auth_token')->plainTextToken;

        Wallet::factory()->create([
            'user_id' => $payer->id,
            'balance' => 1000,
        ]);

        $this->mock(AuthorizerServiceInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/transfer', [
                'payer' => $payer->id,
                'payee' => $payee->id,
                'value' => 100.50,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    "payer",
                    "payee",
                    "value",
                    "status",
                    "updated_at",
                    "created_at",
                    "id",
                ],
            ]);
    }

    public function test_user_merchant_cannot_transfer()
    {
        Exceptions::fake();
        $payer = User::factory()->create([
            'type' => UserTypeEnum::MERCHANT,
        ]);
        $payee = User::factory()->create([
            'type' => UserTypeEnum::MERCHANT,
        ]);
        $token = $payer->createToken('auth_token')->plainTextToken;

        Wallet::factory()->create([
            'user_id' => $payer->id,
            'balance' => 1000,
        ]);

        $this->mock(AuthorizerServiceInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/transfer', [
                'payer' => $payer->id,
                'payee' => $payee->id,
                'value' => 100.50,
            ]);

        $response->assertStatus(403)
            ->assertExactJson([
                'status' => 'fail',
                'data' => [
                    'message' => "Oops! Usuários lojistas não podem fazer transferências.",
                ],
            ]);
    }

    public function test_user_cannot_transfer_to_themself()
    {
        Exceptions::fake();
        $payer = User::factory()->create([
            'type' => UserTypeEnum::COMMON,
        ]);
        $token = $payer->createToken('auth_token')->plainTextToken;

        Wallet::factory()->create([
            'user_id' => $payer->id,
            'balance' => 1000,
        ]);

        $this->mock(AuthorizerServiceInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/transfer', [
                'payer' => $payer->id,
                'payee' => $payer->id,
                'value' => 100.50,
            ]);

        $response->assertStatus(403)
            ->assertExactJson([
                'status' => 'fail',
                'data' => [
                    'message' => "Oops! Você não pode transferir saldo para você mesmo!",
                ],
            ]);
    }

    public function test_user_cannot_transfer_without_balance()
    {
        Exceptions::fake();
        $payer = User::factory()->create([
            'type' => UserTypeEnum::COMMON,
        ]);
        $payee = User::factory()->create([
            'type' => UserTypeEnum::MERCHANT,
        ]);
        $token = $payer->createToken('auth_token')->plainTextToken;

        Wallet::factory()->create([
            'user_id' => $payer->id,
            'balance' => 1000,
        ]);

        $this->mock(AuthorizerServiceInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/transfer', [
                'payer' => $payer->id,
                'payee' => $payee->id,
                'value' => 100000.50,
            ]);

        $response->assertStatus(403)
            ->assertExactJson([
                'status' => 'fail',
                'data' => [
                    'message' => "Oops! Você não possui saldo suficiente. :(",
                ],
            ]);
    }

    public function test_user_cannot_transfer_using_another_persons_id()
    {
        Exceptions::fake();
        $userAuthenticated = User::factory()->create([
            'type' => UserTypeEnum::COMMON,
        ]);
        $payer = User::factory()->create([
            'type' => UserTypeEnum::COMMON,
        ]);
        $payee = User::factory()->create([
            'type' => UserTypeEnum::MERCHANT,
        ]);
        $token = $userAuthenticated->createToken('auth_token')->plainTextToken;

        Wallet::factory()->create([
            'user_id' => $payer->id,
            'balance' => 1000,
        ]);

        $this->mock(AuthorizerServiceInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/transfer', [
                'payer' => $payer->id,
                'payee' => $payee->id,
                'value' => 10.50,
            ]);

        $response->assertStatus(403)
            ->assertExactJson([
                'status' => 'fail',
                'data' => [
                    'message' => "Oops! Não é possível realizar essa transação!",
                ],
            ]);
    }
}
