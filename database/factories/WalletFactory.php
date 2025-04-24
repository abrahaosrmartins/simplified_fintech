<?php

namespace Database\Factories;

use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\Wallet\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = DB::table('users')->select()->where('type', UserTypeEnum::COMMON)->first();
        return [
            'user_id' => $user->id,
            'balance' => 1000,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
