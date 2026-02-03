<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BalanceTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'balance_transactions';
    protected $primaryKey = 'transaction_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'balance_uuid',
        'user_uuid',
        'transaction_type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'reference_id',
        'payment_method',
        'payment_gateway',
        'gateway_transaction_id',
        'status',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function balance()
    {
        return $this->belongsTo(UserBalance::class, 'balance_uuid', 'balance_uuid');
    }

    public function user()
    {
        return $this->belongsTo(FusionUser::class, 'user_uuid', 'user_uuid');
    }
}
