<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a call is answered
 */
class CallAnswered
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

    public function getAnswerTimestamp(): ?string
    {
        return $this->eventData['Caller-Channel-Answered-Time'] ?? null;
    }
}
