<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class VoiceBroadcast extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'voice_broadcasts';
    protected $primaryKey = 'broadcast_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'domain_uuid',
        'broadcast_name',
        'broadcast_description',
        'audio_file_path',
        'audio_url',
        'text_to_speech',
        'tts_text',
        'tts_voice',
        'tts_language',
        'caller_id_name',
        'caller_id_number',
        'max_retries',
        'retry_delay',
        'call_timeout',
        'broadcast_status',
        'start_time',
        'end_time',
        'scheduled_at',
        'total_contacts',
        'called_count',
        'answered_count',
        'failed_count',
        'completion_rate',
        'ai_enabled',
        'ai_config',
        'ai_conversation_mode',
        'ai_model',
        'ai_system_prompt',
        'ai_voice_settings',
        'ai_speech_recognition',
        'ai_natural_language',
        'ai_max_conversation_turns',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'scheduled_at' => 'datetime',
        'text_to_speech' => 'boolean',
        'max_retries' => 'integer',
        'retry_delay' => 'integer',
        'call_timeout' => 'integer',
        'total_contacts' => 'integer',
        'called_count' => 'integer',
        'answered_count' => 'integer',
        'failed_count' => 'integer',
        'completion_rate' => 'decimal:2',
        'ai_enabled' => 'boolean',
        'ai_config' => 'array',
        'ai_conversation_mode' => 'boolean',
        'ai_voice_settings' => 'array',
        'ai_speech_recognition' => 'boolean',
        'ai_natural_language' => 'boolean',
        'ai_max_conversation_turns' => 'integer',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    public function messages()
    {
        return $this->hasMany(BroadcastMessage::class, 'broadcast_uuid', 'broadcast_uuid');
    }

    public function contacts()
    {
        return $this->belongsToMany(CampaignContact::class, 'broadcast_contacts', 'broadcast_uuid', 'contact_uuid');
    }
}
