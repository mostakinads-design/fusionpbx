<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XmlCdr extends Model
{
    protected $table = 'v_xml_cdr';
    protected $primaryKey = 'xml_cdr_uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'insert_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'xml_cdr_uuid',
        'domain_uuid',
        'extension_uuid',
        'domain_name',
        'accountcode',
        'direction',
        'context',
        'caller_id_name',
        'caller_id_number',
        'caller_destination',
        'source_number',
        'destination_number',
        'start_epoch',
        'start_stamp',
        'answer_epoch',
        'answer_stamp',
        'end_epoch',
        'end_stamp',
        'duration',
        'mduration',
        'billsec',
        'billmsec',
        'bridge_uuid',
        'read_codec',
        'read_rate',
        'write_codec',
        'write_rate',
        'remote_media_ip',
        'network_addr',
        'recording_file',
        'leg',
        'missed_call',
        'hangup_cause',
        'hangup_cause_q850',
        'cc_side',
        'cc_member_uuid',
        'cc_queue_joined_epoch',
        'cc_queue',
        'cc_member_session_uuid',
        'cc_agent',
        'cc_agent_type',
        'cc_agent_bridged',
        'cc_queue_answered_epoch',
        'cc_queue_terminated_epoch',
        'cc_queue_canceled_epoch',
        'cc_cancel_reason',
        'cc_cause',
        'waitsec',
        'conference_name',
        'conference_uuid',
        'conference_member_id',
        'digits_dialed',
        'pin_number',
        'sip_hangup_disposition',
        'xml',
        'json',
    ];

    protected $casts = [
        'start_epoch' => 'integer',
        'answer_epoch' => 'integer',
        'end_epoch' => 'integer',
        'duration' => 'integer',
        'mduration' => 'integer',
        'billsec' => 'integer',
        'billmsec' => 'integer',
        'waitsec' => 'integer',
        'missed_call' => 'boolean',
        'cc_agent_bridged' => 'boolean',
        'start_stamp' => 'datetime',
        'answer_stamp' => 'datetime',
        'end_stamp' => 'datetime',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    public function extension()
    {
        return $this->belongsTo(Extension::class, 'extension_uuid', 'extension_uuid');
    }
}
