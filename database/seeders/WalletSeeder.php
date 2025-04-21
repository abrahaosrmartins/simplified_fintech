<?php

namespace Database\Seeders;

use App\Domain\User\Enums\UserTypeEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commonUser = DB::table('users')->select()->where('type', UserTypeEnum::COMMON)->first();
        $merchantUser = DB::table('users')->select()->where('type', UserTypeEnum::MERCHANT)->first();

        DB::table('wallets')->insert([
            'user_id' => $commonUser->id,
            'balance' => 1000.00,
        ]);

        DB::table('wallets')->insert([
            'user_id' => $merchantUser->id,
            'balance' => 1000.00,
        ]);
    }
}
