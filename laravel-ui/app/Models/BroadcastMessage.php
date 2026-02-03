<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BroadcastMessage extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'broadcast_messages';
    protected $primaryKey = 'broadcast_message_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'broadcast_uuid',
        'contact_uuid',
        'phone_number',
        'call_status',
        'call_duration',
        'call_answered',
        'attempts',
        'last_attempt_at',
        'completed_at',
        'recording_url',
        'failure_reason',
    ];

    protected $casts = [
        'call_answered' => 'boolean',
        'call_duration' => 'integer',
        'attempts' => 'integer',
        'last_attempt_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function broadcast()
    {
        return $this->belongsTo(VoiceBroadcast::class, 'broadcast_uuid', 'broadcast_uuid');
    }

    public function contact()
    {
        return $this->belongsTo(CampaignContact::class, 'contact_uuid', 'contact_uuid');
    }
}
