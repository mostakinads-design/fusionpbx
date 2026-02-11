<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * XmlCdr Model
 * 
 * Call Detail Records (CDR) from FreeSwitch.
 * Complete call history with all metadata.
 *
 * @property string $xml_cdr_uuid
 * @property string $domain_uuid
 * @property string|null $extension_uuid
 * @property string|null $domain_name
 * @property string|null $accountcode
 * @property string|null $direction
 * @property string|null $caller_id_name
 * @property string|null $caller_id_number
 * @property string|null $destination_number
 * @property int|null $start_epoch
 * @property \Illuminate\Support\Carbon|null $start_stamp
 * @property \Illuminate\Support\Carbon|null $answer_stamp
 * @property int|null $answer_epoch
 * @property int|null $end_epoch
 * @property \Illuminate\Support\Carbon|null $end_stamp
 * @property int|null $duration
 * @property int|null $billsec
 * @property string|null $hangup_cause
 * @property string|null $record_path
 * @property string|null $record_name
 * @property string|null $leg
 * @property bool $missed_call
 */
class XmlCdr extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_xml_cdr';
    protected $primaryKey = 'xml_cdr_uuid';
    
    protected $fillable = [
        'domain_uuid',
        'extension_uuid',
        'domain_name',
        'accountcode',
        'direction',
        'caller_id_name',
        'caller_id_number',
        'destination_number',
        'start_epoch',
        'start_stamp',
        'answer_stamp',
        'answer_epoch',
        'end_epoch',
        'end_stamp',
        'duration',
        'billsec',
        'hangup_cause',
        'record_path',
        'record_name',
        'leg',
        'missed_call',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_epoch' => 'integer',
        'answer_epoch' => 'integer',
        'end_epoch' => 'integer',
        'duration' => 'integer',
        'billsec' => 'integer',
        'missed_call' => 'boolean',
        'start_stamp' => 'datetime',
        'answer_stamp' => 'datetime',
        'end_stamp' => 'datetime',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the domain.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Get the extension.
     */
    public function extension(): BelongsTo
    {
        return $this->belongsTo(FusionPbxExtension::class, 'extension_uuid', 'extension_uuid');
    }

    /**
     * Check if the call was answered.
     */
    public function wasAnswered(): bool
    {
        return !is_null($this->answer_stamp) && $this->billsec > 0;
    }

    /**
     * Check if call was inbound.
     */
    public function isInbound(): bool
    {
        return $this->direction === 'inbound';
    }

    /**
     * Check if call was outbound.
     */
    public function isOutbound(): bool
    {
        return $this->direction === 'outbound';
    }

    /**
     * Check if call has a recording.
     */
    public function hasRecording(): bool
    {
        return !empty($this->record_path);
    }

    /**
     * Scope for answered calls.
     */
    public function scopeAnswered($query)
    {
        return $query->whereNotNull('answer_stamp')->where('billsec', '>', 0);
    }

    /**
     * Scope for missed calls.
     */
    public function scopeMissed($query)
    {
        return $query->where('missed_call', true);
    }

    /**
     * Scope for inbound calls.
     */
    public function scopeInbound($query)
    {
        return $query->where('direction', 'inbound');
    }

    /**
     * Scope for outbound calls.
     */
    public function scopeOutbound($query)
    {
        return $query->where('direction', 'outbound');
    }

    /**
     * Scope for recent calls.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('start_stamp', '>=', now()->subDays($days));
    }

    /**
     * Scope by caller.
     */
    public function scopeByCaller($query, string $number)
    {
        return $query->where('caller_id_number', 'like', "%{$number}%");
    }

    /**
     * Scope by destination.
     */
    public function scopeByDestination($query, string $number)
    {
        return $query->where('destination_number', 'like', "%{$number}%");
    }

    /**
     * Scope with recordings.
     */
    public function scopeWithRecordings($query)
    {
        return $query->whereNotNull('record_path');
    }
}
