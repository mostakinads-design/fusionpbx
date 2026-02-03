<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CampaignContact extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'campaign_contacts';
    protected $primaryKey = 'contact_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'campaign_uuid',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'custom_data',
        'status',
        'priority',
        'attempts',
        'last_call_at',
        'next_call_at',
    ];

    protected $casts = [
        'custom_data' => 'array',
        'priority' => 'integer',
        'attempts' => 'integer',
        'last_call_at' => 'datetime',
        'next_call_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_uuid', 'campaign_uuid');
    }

    public function calls()
    {
        return $this->hasMany(CampaignCall::class, 'contact_uuid', 'contact_uuid');
    }
}
