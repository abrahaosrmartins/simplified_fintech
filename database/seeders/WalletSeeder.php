<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->select('id', 'type')->get();

        $wallets = $users->map(function ($user) {
            return [
                'user_id' => $user->id,
                'balance' => 1000,
                'created_at' => now(),
                'updated_at' => now()
            ];
        })->toArray();

        DB::table('wallets')->insert($wallets);
    }
}
