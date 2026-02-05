<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CallCenterQueue extends Model
{
    protected $table = 'v_call_center_queues';
    protected $primaryKey = 'call_center_queue_uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'insert_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'call_center_queue_uuid',
        'domain_uuid',
        'queue_name',
        'queue_extension',
        'queue_greeting',
        'queue_strategy',
        'queue_moh_sound',
        'queue_record_template',
        'queue_time_base_score',
        'queue_max_wait_time',
        'queue_max_wait_time_with_no_agent',
        'queue_max_wait_time_with_no_agent_time_reached',
        'queue_tier_rules_apply',
        'queue_tier_rule_wait_second',
        'queue_tier_rule_wait_multiply_level',
        'queue_tier_rule_no_agent_no_wait',
        'queue_discard_abandoned_after',
        'queue_abandoned_resume_allowed',
        'queue_cid_prefix',
        'queue_announce_position',
        'queue_announce_sound',
        'queue_announce_frequency',
        'queue_cc_exit_keys',
        'queue_description',
        'queue_enabled',
        'insert_user',
        'update_user',
    ];

    protected $casts = [
        'queue_enabled' => 'boolean',
        'queue_tier_rules_apply' => 'boolean',
        'queue_tier_rule_no_agent_no_wait' => 'boolean',
        'queue_abandoned_resume_allowed' => 'boolean',
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
        return $this->hasMany(CallCenterTier::class, 'queue_name', 'queue_name');
    }

    public function agents()
    {
        return $this->hasManyThrough(
            CallCenterAgent::class,
            CallCenterTier::class,
            'queue_name',
            'agent_name',
            'queue_name',
            'agent_name'
        );
    }
}
