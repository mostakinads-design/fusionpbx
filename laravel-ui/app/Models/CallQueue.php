<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CallQueue extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'call_queues';
    protected $primaryKey = 'queue_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'domain_uuid',
        'queue_name',
        'queue_extension',
        'queue_description',
        'strategy',
        'timeout',
        'max_wait_time',
        'max_wait_time_with_no_agent',
        'max_wait_time_with_no_agent_time_reached',
        'tier_rules_apply',
        'tier_rule_wait_second',
        'tier_rule_no_agent_no_wait',
        'discard_abandoned_after',
        'abandoned_resume_allowed',
        'moh_sound',
        'announce_sound',
        'announce_frequency',
        'record_calls',
        'is_active',
        'caller_id_name_prefix',
        'caller_id_number_prefix',
    ];

    protected $casts = [
        'timeout' => 'integer',
        'max_wait_time' => 'integer',
        'max_wait_time_with_no_agent' => 'integer',
        'tier_rule_wait_second' => 'integer',
        'discard_abandoned_after' => 'integer',
        'announce_frequency' => 'integer',
        'tier_rules_apply' => 'boolean',
        'tier_rule_no_agent_no_wait' => 'boolean',
        'abandoned_resume_allowed' => 'boolean',
        'record_calls' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    public function agents()
    {
        return $this->belongsToMany(Agent::class, 'queue_agents', 'queue_uuid', 'agent_uuid')
            ->withPivot('tier_level', 'tier_position')
            ->withTimestamps();
    }

    public function calls()
    {
        return $this->hasMany(QueueCall::class, 'queue_uuid', 'queue_uuid');
    }

    public function waitingCalls()
    {
        return $this->hasMany(QueueCall::class, 'queue_uuid', 'queue_uuid')
            ->where('status', 'waiting');
    }

    public function activeCalls()
    {
        return $this->hasMany(QueueCall::class, 'queue_uuid', 'queue_uuid')
            ->whereIn('status', ['ringing', 'answered']);
    }
}
