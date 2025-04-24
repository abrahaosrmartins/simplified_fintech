<?php

namespace Database\Seeders;

use App\Domain\User\Enums\DocumentTypeEnum;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::factory()->count(2)->create();
        User::factory()->create([
            'email' => 'merchant@user.com',
            'document' => '32165498742',
            'document_type' => DocumentTypeEnum::CNPJ,
            'type' => UserTypeEnum::MERCHANT
        ]);
    }
}
