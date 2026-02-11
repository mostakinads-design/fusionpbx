<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a channel is created
 */
class ChannelCreate
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

    public function getChannelName(): ?string
    {
        return $this->eventData['Channel-Name'] ?? null;
    }

    public function getDirection(): ?string
    {
        return $this->eventData['Call-Direction'] ?? null;
    }
}
