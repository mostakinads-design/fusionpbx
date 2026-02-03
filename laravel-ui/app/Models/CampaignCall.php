<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CampaignCall extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'campaign_calls';
    protected $primaryKey = 'call_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'campaign_uuid',
        'contact_uuid',
        'agent_uuid',
        'call_sid',
        'phone_number',
        'call_status',
        'disposition',
        'call_duration',
        'answered_at',
        'ended_at',
        'recording_url',
        'notes',
        'is_ai_handled',
        'ai_transcript',
        'ai_sentiment',
    ];

    protected $casts = [
        'call_duration' => 'integer',
        'answered_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_ai_handled' => 'boolean',
        'ai_transcript' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_uuid', 'campaign_uuid');
    }

    public function contact()
    {
        return $this->belongsTo(CampaignContact::class, 'contact_uuid', 'contact_uuid');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_uuid', 'agent_uuid');
    }
}
