<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a new call is created in FreeSwitch
 */
class CallCreated
{
    use Dispatchable, SerializesModels;

    /**
     * Event data from FreeSwitch
     */
    public array $eventData;

    /**
     * Create a new event instance
     */
    public function __construct(array $eventData)
    {
        $this->eventData = $eventData;
    }

    /**
     * Get the channel UUID
     */
    public function getChannelUuid(): ?string
    {
        return $this->eventData['Unique-ID'] ?? null;
    }

    /**
     * Get caller ID number
     */
    public function getCallerIdNumber(): ?string
    {
        return $this->eventData['Caller-Caller-ID-Number'] ?? null;
    }

    /**
     * Get destination number
     */
    public function getDestinationNumber(): ?string
    {
        return $this->eventData['Caller-Destination-Number'] ?? null;
    }
}
