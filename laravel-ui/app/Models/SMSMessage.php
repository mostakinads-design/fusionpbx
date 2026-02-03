<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SMSMessage extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sms_messages';
    protected $primaryKey = 'sms_message_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sms_campaign_uuid',
        'contact_uuid',
        'phone_number',
        'message_content',
        'status',
        'sent_at',
        'delivered_at',
        'failed_at',
        'failure_reason',
        'provider_message_id',
        'provider_name',
        'cost',
        'direction',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
        'cost' => 'decimal:4',
    ];

    public function campaign()
    {
        return $this->belongsTo(SMSCampaign::class, 'sms_campaign_uuid', 'sms_campaign_uuid');
    }

    public function contact()
    {
        return $this->belongsTo(CampaignContact::class, 'contact_uuid', 'contact_uuid');
    }
}
