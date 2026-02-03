<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Campaign extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'campaigns';
    protected $primaryKey = 'campaign_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'domain_uuid',
        'campaign_name',
        'campaign_description',
        'campaign_type',
        'dialing_mode',
        'max_attempts',
        'retry_delay',
        'call_timeout',
        'start_time',
        'end_time',
        'days_of_week',
        'ai_agent_enabled',
        'ai_agent_config',
        'campaign_status',
        'caller_id_name',
        'caller_id_number',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'days_of_week' => 'array',
        'ai_agent_enabled' => 'boolean',
        'ai_agent_config' => 'array',
        'max_attempts' => 'integer',
        'retry_delay' => 'integer',
        'call_timeout' => 'integer',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    public function contacts()
    {
        return $this->hasMany(CampaignContact::class, 'campaign_uuid', 'campaign_uuid');
    }

    public function calls()
    {
        return $this->hasMany(CampaignCall::class, 'campaign_uuid', 'campaign_uuid');
    }

    public function agents()
    {
        return $this->belongsToMany(Agent::class, 'campaign_agents', 'campaign_uuid', 'agent_uuid');
    }
}
