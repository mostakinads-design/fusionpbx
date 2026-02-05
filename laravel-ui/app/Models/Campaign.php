<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Campaign extends Model
{
    protected $table = 'v_campaigns';
    protected $primaryKey = 'campaign_uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'insert_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'campaign_uuid',
        'domain_uuid',
        'campaign_name',
        'campaign_type',
        'broadcast_type',
        'ai_enabled',
        'ai_provider',
        'ai_model',
        'ai_prompt',
        'voice_message',
        'sms_message',
        'caller_id_number',
        'caller_id_name',
        'scheduled_start',
        'max_retry_attempts',
        'status',
        'total_contacts',
        'calls_made',
        'calls_answered',
        'calls_failed',
        'sms_sent',
        'sms_delivered',
        'insert_user',
        'update_user',
    ];

    protected $casts = [
        'ai_enabled' => 'boolean',
        'scheduled_start' => 'datetime',
        'max_retry_attempts' => 'integer',
        'total_contacts' => 'integer',
        'calls_made' => 'integer',
        'calls_answered' => 'integer',
        'calls_failed' => 'integer',
        'sms_sent' => 'integer',
        'sms_delivered' => 'integer',
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

    public function contacts()
    {
        return $this->hasMany(CampaignContact::class, 'campaign_uuid', 'campaign_uuid');
    }

    public function calls()
    {
        return $this->hasMany(CampaignCall::class, 'campaign_uuid', 'campaign_uuid');
    }
}
