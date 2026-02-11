<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a call is hung up
 */
class CallHangup
{
    use Dispatchable, SerializesModels;

    public array $eventData;

    public function __construct(array $eventData)
    {
        $this->eventData = $eventData;
    }

    public function getChannelUuid(): ?string
    {
        return $this->eventData['Unique-ID'] ?? null;
    }

    public function getHangupCause(): ?string
    {
        return $this->eventData['Hangup-Cause'] ?? null;
    }

    public function getDuration(): ?int
    {
        return isset($this->eventData['variable_duration']) 
            ? (int) $this->eventData['variable_duration'] 
            : null;
    }

    public function getBillSeconds(): ?int
    {
        return isset($this->eventData['variable_billsec']) 
            ? (int) $this->eventData['variable_billsec'] 
            : null;
    }
}
