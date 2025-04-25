<?php

use App\Domain\Transaction\Enums\TransactionStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payer');
            $table->foreign('payer')->references('id')->on('users');
            $table->unsignedBigInteger('payee');
            $table->foreign('payee')->references('id')->on('users');
            $table->decimal('value', 8, 2);
            $table->enum('status', [TransactionStatusEnum::values()])->index('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
