<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LiveCall extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'live_calls';
    protected $primaryKey = 'live_call_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'domain_uuid',
        'call_uuid',
        'direction',
        'caller_id_name',
        'caller_id_number',
        'destination_number',
        'extension_uuid',
        'agent_uuid',
        'queue_uuid',
        'status',
        'start_time',
        'answer_time',
        'channel_name',
        'is_recording',
        'hold_status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'answer_time' => 'datetime',
        'is_recording' => 'boolean',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    public function extension()
    {
        return $this->belongsTo(Extension::class, 'extension_uuid', 'extension_uuid');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_uuid', 'agent_uuid');
    }

    public function queue()
    {
        return $this->belongsTo(CallQueue::class, 'queue_uuid', 'queue_uuid');
    }

    public function getDurationAttribute()
    {
        if (!$this->start_time) {
            return 0;
        }
        
        $endTime = $this->answer_time ?? now();
        return $this->start_time->diffInSeconds($endTime);
    }
}
