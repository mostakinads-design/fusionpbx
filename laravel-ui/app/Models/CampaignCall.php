<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CampaignCall extends Model
{
    protected $table = 'v_campaign_calls';
    protected $primaryKey = 'campaign_call_uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'insert_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'campaign_call_uuid',
        'campaign_uuid',
        'campaign_contact_uuid',
        'domain_uuid',
        'call_uuid',
        'call_status',
        'call_duration',
        'call_answered',
        'sms_sent',
        'sms_status',
        'ai_conversation',
        'ai_extracted_data',
        'insert_user',
        'update_user',
    ];

    protected $casts = [
        'call_duration' => 'integer',
        'call_answered' => 'boolean',
        'sms_sent' => 'boolean',
        'ai_conversation' => 'array',
        'ai_extracted_data' => 'array',
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

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_uuid', 'campaign_uuid');
    }

    public function contact()
    {
        return $this->belongsTo(CampaignContact::class, 'campaign_contact_uuid', 'campaign_contact_uuid');
    }

    public function cdr()
    {
        return $this->belongsTo(XmlCdr::class, 'call_uuid', 'uuid');
    }
}
