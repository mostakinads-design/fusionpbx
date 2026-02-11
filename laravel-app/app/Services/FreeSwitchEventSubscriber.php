<?php

namespace App\Services;

use App\Events\CallCreated;
use App\Events\CallAnswered;
use App\Events\CallHangup;
use App\Events\ChannelCreate;
use Illuminate\Support\Facades\Log;

/**
 * FreeSwitch Event Subscriber Service
 * 
 * Subscribes to FreeSwitch events and dispatches Laravel events
 */
class FreeSwitchEventSubscriber
{
    /**
     * ESL service instance
     */
    protected FreeSwitchEslService $esl;

    /**
     * Should continue listening
     */
    protected bool $shouldListen = true;

    /**
     * Event mapping
     */
    protected array $eventMap = [
        'CHANNEL_CREATE' => ChannelCreate::class,
        'CHANNEL_ANSWER' => CallAnswered::class,
        'CHANNEL_HANGUP' => CallHangup::class,
        'CHANNEL_HANGUP_COMPLETE' => CallHangup::class,
    ];

    /**
     * Create a new event subscriber
     */
    public function __construct(FreeSwitchEslService $esl)
    {
        $this->esl = $esl;
    }

    /**
     * Start listening for events
     * 
     * @param array|null $events Events to subscribe to (null = use config)
     * @return void
     */
    public function listen(?array $events = null): void
    {
        if (!$this->esl->isConnected()) {
            $this->esl->connect();
        }

        $events = $events ?? config('freeswitch.events.subscribe_to', []);
        
        if (empty($events)) {
            Log::warning('No events configured for subscription');
            return;
        }

        // Subscribe to events
        if (!$this->esl->subscribeEvents($events)) {
            Log::error('Failed to subscribe to FreeSwitch events');
            return;
        }

        Log::info('FreeSwitch event listener started', ['events' => $events]);

        // Listen loop
        $this->shouldListen = true;
        while ($this->shouldListen) {
            try {
                $event = $this->esl->readEvent(1);
                
                if ($event) {
                    $this->handleEvent($event);
                }
                
                // Small sleep to prevent CPU spinning
                usleep(1000); // 1ms
                
            } catch (\Exception $e) {
                Log::error('Error reading FreeSwitch event', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Try to reconnect
                sleep(5);
                try {
                    $this->esl->connect();
                    $this->esl->subscribeEvents($events);
                } catch (\Exception $reconnectError) {
                    Log::error('Failed to reconnect to FreeSwitch', [
                        'error' => $reconnectError->getMessage()
                    ]);
                }
            }
        }

        Log::info('FreeSwitch event listener stopped');
    }

    /**
     * Stop listening for events
     */
    public function stop(): void
    {
        $this->shouldListen = false;
    }

    /**
     * Handle a FreeSwitch event
     * 
     * @param array $event Event data
     * @return void
     */
    protected function handleEvent(array $event): void
    {
        $eventName = $event['Event-Name'] ?? null;
        
        if (!$eventName) {
            Log::warning('Received event without Event-Name', ['event' => $event]);
            return;
        }

        Log::debug('Received FreeSwitch event', [
            'event' => $eventName,
            'uuid' => $event['Unique-ID'] ?? 'unknown'
        ]);

        // Dispatch Laravel event based on FreeSwitch event type
        $this->dispatchLaravelEvent($eventName, $event);

        // Call specific handlers
        $handlerMethod = 'handle' . str_replace('_', '', ucwords($eventName, '_'));
        if (method_exists($this, $handlerMethod)) {
            $this->$handlerMethod($event);
        }
    }

    /**
     * Dispatch Laravel event for FreeSwitch event
     * 
     * @param string $eventName FreeSwitch event name
     * @param array $eventData Event data
     * @return void
     */
    protected function dispatchLaravelEvent(string $eventName, array $eventData): void
    {
        $laravelEventClass = $this->eventMap[$eventName] ?? null;
        
        if ($laravelEventClass) {
            event(new $laravelEventClass($eventData));
            Log::debug('Dispatched Laravel event', ['event' => $laravelEventClass]);
        }
    }

    /**
     * Handle CHANNEL_CREATE event
     */
    protected function handleChannelCreate(array $event): void
    {
        // Custom handling for channel creation
        Log::info('Channel created', [
            'uuid' => $event['Unique-ID'] ?? 'unknown',
            'caller' => $event['Caller-Caller-ID-Number'] ?? 'unknown',
            'destination' => $event['Caller-Destination-Number'] ?? 'unknown'
        ]);
    }

    /**
     * Handle CHANNEL_ANSWER event
     */
    protected function handleChannelAnswer(array $event): void
    {
        // Custom handling for answered calls
        Log::info('Call answered', [
            'uuid' => $event['Unique-ID'] ?? 'unknown',
            'caller' => $event['Caller-Caller-ID-Number'] ?? 'unknown'
        ]);
    }

    /**
     * Handle CHANNEL_HANGUP event
     */
    protected function handleChannelHangup(array $event): void
    {
        // Custom handling for hangup
        Log::info('Call hungup', [
            'uuid' => $event['Unique-ID'] ?? 'unknown',
            'cause' => $event['Hangup-Cause'] ?? 'unknown',
            'duration' => $event['variable_duration'] ?? 0
        ]);
    }

    /**
     * Handle CHANNEL_HANGUP_COMPLETE event
     */
    protected function handleChannelHangupComplete(array $event): void
    {
        // Save call detail record
        $this->saveCdr($event);
    }

    /**
     * Save call detail record from event
     * 
     * @param array $event
     * @return void
     */
    protected function saveCdr(array $event): void
    {
        try {
            // Extract CDR data from event
            $cdrData = [
                'xml_cdr_uuid' => $event['Unique-ID'] ?? null,
                'domain_uuid' => $event['variable_domain_uuid'] ?? null,
                'caller_id_number' => $event['Caller-Caller-ID-Number'] ?? null,
                'caller_id_name' => $event['Caller-Caller-ID-Name'] ?? null,
                'destination_number' => $event['Caller-Destination-Number'] ?? null,
                'direction' => $event['Call-Direction'] ?? null,
                'start_stamp' => isset($event['Caller-Channel-Created-Time']) 
                    ? date('Y-m-d H:i:s', $event['Caller-Channel-Created-Time'] / 1000000) 
                    : null,
                'answer_stamp' => isset($event['Caller-Channel-Answered-Time']) 
                    ? date('Y-m-d H:i:s', $event['Caller-Channel-Answered-Time'] / 1000000) 
                    : null,
                'end_stamp' => isset($event['Caller-Channel-Hangup-Time']) 
                    ? date('Y-m-d H:i:s', $event['Caller-Channel-Hangup-Time'] / 1000000) 
                    : null,
                'duration' => $event['variable_duration'] ?? 0,
                'billsec' => $event['variable_billsec'] ?? 0,
                'hangup_cause' => $event['Hangup-Cause'] ?? null,
                'record_path' => $event['variable_record_path'] ?? null,
            ];

            // Only save if we have required fields
            if ($cdrData['xml_cdr_uuid']) {
                // Create or update CDR using XmlCdr model
                // This would typically use the XmlCdr model
                Log::debug('CDR data ready', ['uuid' => $cdrData['xml_cdr_uuid']]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to save CDR', [
                'error' => $e->getMessage(),
                'uuid' => $event['Unique-ID'] ?? 'unknown'
            ]);
        }
    }

    /**
     * Get ESL service instance
     */
    public function getEsl(): FreeSwitchEslService
    {
        return $this->esl;
    }
}
