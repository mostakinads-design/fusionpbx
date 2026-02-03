<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserBalance extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'user_balances';
    protected $primaryKey = 'balance_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_uuid',
        'domain_uuid',
        'balance',
        'currency',
        'credit_limit',
        'auto_recharge_enabled',
        'auto_recharge_amount',
        'auto_recharge_threshold',
        'low_balance_alert_threshold',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'auto_recharge_amount' => 'decimal:2',
        'auto_recharge_threshold' => 'decimal:2',
        'low_balance_alert_threshold' => 'decimal:2',
        'auto_recharge_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(FusionUser::class, 'user_uuid', 'user_uuid');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    public function transactions()
    {
        return $this->hasMany(BalanceTransaction::class, 'balance_uuid', 'balance_uuid');
    }

    /**
     * Check if balance is low
     */
    public function isLowBalance()
    {
        return $this->balance <= $this->low_balance_alert_threshold;
    }

    /**
     * Check if auto recharge should trigger
     */
    public function shouldAutoRecharge()
    {
        return $this->auto_recharge_enabled && 
               $this->balance <= $this->auto_recharge_threshold;
    }

    /**
     * Add funds to balance
     */
    public function addFunds($amount, $description = 'Top-up', $referenceId = null)
    {
        $this->increment('balance', $amount);
        
        return BalanceTransaction::create([
            'transaction_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'balance_uuid' => $this->balance_uuid,
            'user_uuid' => $this->user_uuid,
            'transaction_type' => 'credit',
            'amount' => $amount,
            'balance_before' => $this->balance - $amount,
            'balance_after' => $this->balance,
            'description' => $description,
            'reference_id' => $referenceId,
            'status' => 'completed',
        ]);
    }

    /**
     * Deduct funds from balance
     */
    public function deductFunds($amount, $description = 'Call charge', $referenceId = null)
    {
        if ($this->balance < $amount && ($this->balance + $this->credit_limit) < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $this->decrement('balance', $amount);
        
        return BalanceTransaction::create([
            'transaction_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'balance_uuid' => $this->balance_uuid,
            'user_uuid' => $this->user_uuid,
            'transaction_type' => 'debit',
            'amount' => $amount,
            'balance_before' => $this->balance + $amount,
            'balance_after' => $this->balance,
            'description' => $description,
            'reference_id' => $referenceId,
            'status' => 'completed',
        ]);
    }
}
