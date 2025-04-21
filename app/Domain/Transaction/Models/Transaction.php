<?php

namespace App\Domain\Transaction\Models;

use App\Domain\Transaction\Enums\TransactionStatusEnum;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'payer',
        'payee',
        'value',
        'status'
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TransactionStatusEnum::class
        ];
    }

    public function payeeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee', 'id');
    }

    public function payerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer', 'id');
    }
}
