<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CallCenterAgent extends Model
{
    protected $table = 'v_call_center_agents';
    protected $primaryKey = 'call_center_agent_uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'insert_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'call_center_agent_uuid',
        'domain_uuid',
        'agent_name',
        'agent_type',
        'agent_call_timeout',
        'agent_contact',
        'agent_status',
        'agent_no_answer_delay_time',
        'agent_max_no_answer',
        'agent_wrap_up_time',
        'agent_reject_delay_time',
        'agent_busy_delay_time',
        'agent_logout_on_reject',
        'agent_enabled',
        'insert_user',
        'update_user',
    ];

    protected $casts = [
        'agent_enabled' => 'boolean',
        'agent_logout_on_reject' => 'boolean',
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

    public function tiers()
    {
        return $this->hasMany(CallCenterTier::class, 'agent_name', 'agent_name');
    }

    public function queues()
    {
        return $this->hasManyThrough(
            CallCenterQueue::class,
            CallCenterTier::class,
            'agent_name',
            'queue_name',
            'agent_name',
            'queue_name'
        );
    }

    public function extension()
    {
        return $this->belongsTo(Extension::class, 'agent_name', 'extension');
    }
}
