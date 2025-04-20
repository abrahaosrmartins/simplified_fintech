<?php

namespace Database\Seeders;

use App\Domain\User\Enums\DocumentTypeEnum;
use App\Domain\User\Enums\UserTypeEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Usuário Comum da Silva',
            'email' => 'common@user.com',
            'password' => Hash::make('1234'),
            'document' => '321.654.987.44',
            'document_type' => DocumentTypeEnum::CPF,
            'type' => UserTypeEnum::COMMON
        ]);

        DB::table('users')->insert([
            'name' => 'Usuário Lojista da Silva',
            'email' => 'merchant@user.com',
            'password' => Hash::make('1234'),
            'document' => '321.654.987.42',
            'document_type' => DocumentTypeEnum::CNPJ,
            'type' => UserTypeEnum::MERCHANT
        ]);
    }
}
