<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SMSCampaign extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sms_campaigns';
    protected $primaryKey = 'sms_campaign_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'domain_uuid',
        'campaign_name',
        'campaign_description',
        'sms_template',
        'sender_id',
        'sending_rate',
        'start_time',
        'end_time',
        'campaign_status',
        'total_contacts',
        'sent_count',
        'delivered_count',
        'failed_count',
        'opt_out_count',
        'schedule_type',
        'scheduled_at',
        'ai_enabled',
        'ai_config',
        'ai_reply_handling',
        'ai_model',
        'ai_personality',
        'ai_conversation_context',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'scheduled_at' => 'datetime',
        'sending_rate' => 'integer',
        'total_contacts' => 'integer',
        'sent_count' => 'integer',
        'delivered_count' => 'integer',
        'failed_count' => 'integer',
        'opt_out_count' => 'integer',
        'ai_enabled' => 'boolean',
        'ai_config' => 'array',
        'ai_reply_handling' => 'boolean',
        'ai_conversation_context' => 'array',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    public function messages()
    {
        return $this->hasMany(SMSMessage::class, 'sms_campaign_uuid', 'sms_campaign_uuid');
    }

    public function contacts()
    {
        return $this->belongsToMany(CampaignContact::class, 'sms_campaign_contacts', 'sms_campaign_uuid', 'contact_uuid');
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRateAttribute()
    {
        if ($this->sent_count == 0) return 0;
        return round(($this->delivered_count / $this->sent_count) * 100, 2);
    }
}
