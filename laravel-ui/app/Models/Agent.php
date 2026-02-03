<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Agent extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'agents';
    protected $primaryKey = 'agent_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'domain_uuid',
        'user_uuid',
        'agent_name',
        'agent_type',
        'extension_uuid',
        'agent_status',
        'max_concurrent_calls',
        'skills',
        'is_available',
    ];

    protected $casts = [
        'skills' => 'array',
        'is_available' => 'boolean',
        'max_concurrent_calls' => 'integer',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    public function user()
    {
        return $this->belongsTo(FusionUser::class, 'user_uuid', 'user_uuid');
    }

    public function extension()
    {
        return $this->belongsTo(Extension::class, 'extension_uuid', 'extension_uuid');
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_agents', 'agent_uuid', 'campaign_uuid');
    }

    public function calls()
    {
        return $this->hasMany(CampaignCall::class, 'agent_uuid', 'agent_uuid');
    }
}
