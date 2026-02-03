<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CallRecord extends Model
{
    use HasFactory, HasUuids;

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
     * Indicates if the IDs are auto-incrementing.
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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'domain_uuid',
        'extension_uuid',
        'caller_id_name',
        'caller_id_number',
        'destination_number',
        'start_stamp',
        'answer_stamp',
        'end_stamp',
        'duration',
        'billsec',
        'hangup_cause',
        'direction',
        'recording_file',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_stamp' => 'datetime',
        'answer_stamp' => 'datetime',
        'end_stamp' => 'datetime',
        'duration' => 'integer',
        'billsec' => 'integer',
    ];

    /**
     * Get the domain that owns the call record.
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Get the extension that owns the call record.
     */
    public function extension()
    {
        return $this->belongsTo(Extension::class, 'extension_uuid', 'extension_uuid');
    }
}
