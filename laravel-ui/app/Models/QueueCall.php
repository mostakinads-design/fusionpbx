<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class QueueCall extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'queue_calls';
    protected $primaryKey = 'queue_call_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'queue_uuid',
        'agent_uuid',
        'call_uuid',
        'caller_id_name',
        'caller_id_number',
        'destination_number',
        'status',
        'wait_time',
        'talk_time',
        'entered_at',
        'answered_at',
        'ended_at',
        'abandon_reason',
        'recording_path',
    ];

    protected $casts = [
        'wait_time' => 'integer',
        'talk_time' => 'integer',
        'entered_at' => 'datetime',
        'answered_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function queue()
    {
        return $this->belongsTo(CallQueue::class, 'queue_uuid', 'queue_uuid');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_uuid', 'agent_uuid');
    }
}
