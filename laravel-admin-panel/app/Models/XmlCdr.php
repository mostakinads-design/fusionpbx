<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XmlCdr extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'v_xml_cdr';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'xml_cdr_uuid';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_stamp' => 'datetime',
        'answer_stamp' => 'datetime',
        'end_stamp' => 'datetime',
        'duration' => 'integer',
        'billsec' => 'integer',
        'start_epoch' => 'integer',
        'answer_epoch' => 'integer',
        'end_epoch' => 'integer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain_uuid',
        'caller_id_name',
        'caller_id_number',
        'destination_number',
        'direction',
        'start_stamp',
        'answer_stamp',
        'end_stamp',
        'duration',
        'billsec',
        'hangup_cause',
        'record_path',
        'record_name',
    ];

    /**
     * Scope a query to only include calls from a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_stamp', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include calls with a specific direction.
     */
    public function scopeDirection($query, $direction)
    {
        return $query->where('direction', $direction);
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute()
    {
        $seconds = $this->duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Get call status based on hangup cause.
     */
    public function getCallStatusAttribute()
    {
        return match($this->hangup_cause) {
            'NORMAL_CLEARING' => 'Answered',
            'ORIGINATOR_CANCEL', 'NO_ANSWER' => 'No Answer',
            'USER_BUSY' => 'Busy',
            default => $this->hangup_cause
        };
    }
}
