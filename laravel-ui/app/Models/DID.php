<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DID extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'dids';
    protected $primaryKey = 'did_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'domain_uuid',
        'did_number',
        'did_description',
        'destination_type',
        'destination_number',
        'extension_uuid',
        'ivr_uuid',
        'queue_uuid',
        'routing_type',
        'voice_enabled',
        'sms_enabled',
        'country_code',
        'area_code',
        'is_active',
        'caller_id_name',
        'caller_id_number',
        'record_calls',
    ];

    protected $casts = [
        'voice_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'is_active' => 'boolean',
        'record_calls' => 'boolean',
    ];

    /**
     * Get the domain that owns the DID
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Get the extension for this DID
     */
    public function extension()
    {
        return $this->belongsTo(Extension::class, 'extension_uuid', 'extension_uuid');
    }

    /**
     * Get the IVR for this DID
     */
    public function ivr()
    {
        return $this->belongsTo(IVR::class, 'ivr_uuid', 'ivr_uuid');
    }

    /**
     * Get the queue for this DID
     */
    public function queue()
    {
        return $this->belongsTo(CallQueue::class, 'queue_uuid', 'queue_uuid');
    }

    /**
     * Get call records for this DID
     */
    public function callRecords()
    {
        return $this->hasMany(CallRecord::class, 'destination_number', 'did_number');
    }

    /**
     * Get formatted DID number with country code
     */
    public function getFormattedNumberAttribute()
    {
        if ($this->country_code) {
            return '+' . $this->country_code . $this->did_number;
        }
        return $this->did_number;
    }

    /**
     * Get routing type label
     */
    public function getRoutingTypeLabel()
    {
        $labels = [];
        if ($this->voice_enabled) {
            $labels[] = 'Voice';
        }
        if ($this->sms_enabled) {
            $labels[] = 'SMS';
        }
        return implode(' + ', $labels) ?: 'None';
    }

    /**
     * Get destination display name
     */
    public function getDestinationDisplay()
    {
        switch ($this->destination_type) {
            case 'extension':
                return $this->extension ? $this->extension->extension . ' (' . $this->extension->effective_caller_id_name . ')' : $this->destination_number;
            case 'ivr':
                return $this->ivr ? $this->ivr->ivr_name : $this->destination_number;
            case 'queue':
                return $this->queue ? $this->queue->queue_name : $this->destination_number;
            case 'external':
                return $this->destination_number;
            default:
                return $this->destination_number;
        }
    }
}
