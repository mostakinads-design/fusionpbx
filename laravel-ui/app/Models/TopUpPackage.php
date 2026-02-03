<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TopUpPackage extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'topup_packages';
    protected $primaryKey = 'package_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'domain_uuid',
        'package_name',
        'package_description',
        'amount',
        'bonus_amount',
        'price',
        'currency',
        'validity_days',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'price' => 'decimal:2',
        'validity_days' => 'integer',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Get total amount including bonus
     */
    public function getTotalAmountAttribute()
    {
        return $this->amount + $this->bonus_amount;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->price <= 0 || $this->amount <= 0) {
            return 0;
        }
        
        $regularPrice = $this->amount;
        $discount = (($regularPrice - $this->price) / $regularPrice) * 100;
        
        return round($discount, 2);
    }
}
