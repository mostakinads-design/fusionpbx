<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CallCenterTier extends Model
{
    protected $table = 'v_call_center_tiers';
    protected $primaryKey = 'call_center_tier_uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'insert_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'call_center_tier_uuid',
        'domain_uuid',
        'agent_name',
        'queue_name',
        'tier_level',
        'tier_position',
        'insert_user',
        'update_user',
    ];

    protected $casts = [
        'tier_level' => 'integer',
        'tier_position' => 'integer',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    public function agent()
    {
        return $this->belongsTo(CallCenterAgent::class, 'agent_name', 'agent_name');
    }

    public function queue()
    {
        return $this->belongsTo(CallCenterQueue::class, 'queue_name', 'queue_name');
    }
}
