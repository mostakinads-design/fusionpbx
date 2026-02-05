<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CampaignContact extends Model
{
    protected $table = 'v_campaign_contacts';
    protected $primaryKey = 'campaign_contact_uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'insert_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'campaign_contact_uuid',
        'campaign_uuid',
        'domain_uuid',
        'contact_name',
        'contact_phone',
        'contact_email',
        'contact_status',
        'call_attempts',
        'last_call_time',
        'sms_sent',
        'sms_delivered',
        'insert_user',
        'update_user',
    ];

    protected $casts = [
        'call_attempts' => 'integer',
        'last_call_time' => 'datetime',
        'sms_sent' => 'boolean',
        'sms_delivered' => 'boolean',
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

    public function calls()
    {
        return $this->hasMany(CampaignCall::class, 'campaign_contact_uuid', 'campaign_contact_uuid');
    }
}
