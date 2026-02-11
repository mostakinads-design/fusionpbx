<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * FreeSwitch Event Socket Library (ESL) Service
 * 
 * Provides communication with FreeSwitch via the Event Socket Layer protocol.
 * Supports command execution, event subscription, and call control.
 */
class FreeSwitchEslService
{
    /**
     * Socket connection resource
     */
    protected $socket = null;

    /**
     * Connection configuration
     */
    protected string $host;
    protected int $port;
    protected string $password;
    protected int $timeout;

    /**
     * Connection state
     */
    protected bool $connected = false;
    protected bool $authenticated = false;

    /**
     * Event subscription state
     */
    protected array $subscribedEvents = [];

    /**
     * Create a new ESL service instance.
     */
    public function __construct()
    {
        $this->host = config('freeswitch.esl.host', '127.0.0.1');
        $this->port = config('freeswitch.esl.port', 8021);
        $this->password = config('freeswitch.esl.password', 'ClueCon');
        $this->timeout = config('freeswitch.esl.timeout', 10);
    }

    /**
     * Connect to FreeSwitch ESL
     * 
     * @return bool
     * @throws EslConnectionException
     */
    public function connect(): bool
    {
        if ($this->connected) {
            return true;
        }

        try {
            $this->socket = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
            
            if (!$this->socket) {
                throw new Exception("Failed to connect to FreeSwitch ESL: {$errstr} ({$errno})");
            }

            // Set socket to non-blocking mode
            stream_set_blocking($this->socket, false);
            
            // Read welcome message
            $response = $this->readResponse();
            
            if (!str_contains($response, 'Content-Type: auth/request')) {
                throw new Exception("Unexpected response from FreeSwitch: {$response}");
            }

            $this->connected = true;
            
            // Authenticate
            return $this->authenticate();

        } catch (Exception $e) {
            Log::error('FreeSwitch ESL connection failed', [
                'host' => $this->host,
                'port' => $this->port,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Authenticate with FreeSwitch
     * 
     * @return bool
     */
    protected function authenticate(): bool
    {
        if ($this->authenticated) {
            return true;
        }

        $this->sendCommand("auth {$this->password}");
        $response = $this->readResponse();

        if (str_contains($response, 'Reply-Text: +OK accepted')) {
            $this->authenticated = true;
            Log::info('FreeSwitch ESL authenticated successfully');
            return true;
        }

        Log::error('FreeSwitch ESL authentication failed', ['response' => $response]);
        return false;
    }

    /**
     * Disconnect from FreeSwitch
     */
    public function disconnect(): void
    {
        if ($this->socket) {
            fclose($this->socket);
            $this->socket = null;
            $this->connected = false;
            $this->authenticated = false;
            Log::info('FreeSwitch ESL disconnected');
        }
    }

    /**
     * Check if connected to FreeSwitch
     * 
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->connected && $this->authenticated && $this->socket !== null;
    }

    /**
     * Send a command to FreeSwitch
     * 
     * @param string $command
     * @return bool
     */
    protected function sendCommand(string $command): bool
    {
        if (!$this->socket) {
            return false;
        }

        $command = trim($command) . "\n\n";
        $result = fwrite($this->socket, $command);
        
        return $result !== false;
    }

    /**
     * Read response from FreeSwitch
     * 
     * @param int $timeout Custom timeout in seconds
     * @return string
     */
    protected function readResponse(int $timeout = null): string
    {
        $timeout = $timeout ?? $this->timeout;
        $buffer = '';
        $startTime = time();

        while (true) {
            if (time() - $startTime > $timeout) {
                break;
            }

            $chunk = fread($this->socket, 8192);
            if ($chunk === false || $chunk === '') {
                usleep(10000); // Sleep 10ms
                continue;
            }

            $buffer .= $chunk;

            // Check if we have a complete response
            if ($this->isCompleteResponse($buffer)) {
                break;
            }
        }

        return $buffer;
    }

    /**
     * Check if response is complete
     * 
     * @param string $buffer
     * @return bool
     */
    protected function isCompleteResponse(string $buffer): bool
    {
        // Check for double newline indicating end of response
        if (str_contains($buffer, "\n\n")) {
            // Parse Content-Length if present
            if (preg_match('/Content-Length: (\d+)/i', $buffer, $matches)) {
                $contentLength = (int) $matches[1];
                $headerEnd = strpos($buffer, "\n\n") + 2;
                $bodyLength = strlen($buffer) - $headerEnd;
                
                return $bodyLength >= $contentLength;
            }
            return true;
        }
        return false;
    }

    /**
     * Execute an API command
     * 
     * @param string $command API command to execute
     * @param array $args Command arguments
     * @return string Response from FreeSwitch
     */
    public function api(string $command, array $args = []): string
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $cmdString = "api {$command}";
        if (!empty($args)) {
            $cmdString .= ' ' . implode(' ', $args);
        }

        $this->sendCommand($cmdString);
        $response = $this->readResponse();

        return $this->parseApiResponse($response);
    }

    /**
     * Execute a background API command
     * 
     * @param string $command API command to execute
     * @param array $args Command arguments
     * @return string Job UUID
     */
    public function bgapi(string $command, array $args = []): string
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $cmdString = "bgapi {$command}";
        if (!empty($args)) {
            $cmdString .= ' ' . implode(' ', $args);
        }

        $this->sendCommand($cmdString);
        $response = $this->readResponse();

        // Extract Job-UUID from response
        if (preg_match('/Job-UUID: ([a-f0-9\-]+)/i', $response, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Parse API response to extract body
     * 
     * @param string $response
     * @return string
     */
    protected function parseApiResponse(string $response): string
    {
        if (preg_match('/Content-Type: api\/response.*?\n\n(.*)/s', $response, $matches)) {
            return trim($matches[1]);
        }

        if (preg_match('/Reply-Text: (.+)/i', $response, $matches)) {
            return trim($matches[1]);
        }

        return trim($response);
    }

    /**
     * Originate a new call
     * 
     * @param string $destination Destination to dial (e.g., user/1001)
     * @param string $extension Extension or application to execute
     * @param string $dialplan Dialplan context
     * @param string $callerIdNumber Caller ID number
     * @param string $callerIdName Caller ID name
     * @param array $variables Channel variables
     * @return string Call UUID or error message
     */
    public function originate(
        string $destination,
        string $extension,
        string $dialplan = 'XML',
        string $callerIdNumber = '',
        string $callerIdName = '',
        array $variables = []
    ): string {
        $cmd = "originate {$destination} {$extension}";
        
        if ($dialplan) {
            $cmd .= " {$dialplan}";
        }

        if ($callerIdName) {
            $cmd .= " '{$callerIdName}'";
        }

        if ($callerIdNumber) {
            $cmd .= " '{$callerIdNumber}'";
        }

        // Add channel variables
        if (!empty($variables)) {
            $varString = '{' . implode(',', array_map(
                fn($k, $v) => "{$k}={$v}",
                array_keys($variables),
                $variables
            )) . '}';
            $cmd = str_replace("originate ", "originate {$varString}", $cmd);
        }

        return $this->api('originate', explode(' ', trim(str_replace('originate ', '', $cmd))));
    }

    /**
     * Hangup a call
     * 
     * @param string $uuid Channel UUID
     * @param string $cause Hangup cause (optional)
     * @return string
     */
    public function hangup(string $uuid, string $cause = 'NORMAL_CLEARING'): string
    {
        return $this->api('uuid_kill', [$uuid, $cause]);
    }

    /**
     * Answer a call
     * 
     * @param string $uuid Channel UUID
     * @return string
     */
    public function answer(string $uuid): string
    {
        return $this->api('uuid_answer', [$uuid]);
    }

    /**
     * Bridge two channels
     * 
     * @param string $uuid1 First channel UUID
     * @param string $uuid2 Second channel UUID
     * @return string
     */
    public function bridge(string $uuid1, string $uuid2): string
    {
        return $this->api('uuid_bridge', [$uuid1, $uuid2]);
    }

    /**
     * Transfer a call
     * 
     * @param string $uuid Channel UUID
     * @param string $extension Destination extension
     * @param string $dialplan Dialplan context
     * @param string $context Context
     * @return string
     */
    public function transfer(string $uuid, string $extension, string $dialplan = 'XML', string $context = 'default'): string
    {
        return $this->api('uuid_transfer', [$uuid, $extension, $dialplan, $context]);
    }

    /**
     * Park a call
     * 
     * @param string $uuid Channel UUID
     * @return string
     */
    public function park(string $uuid): string
    {
        return $this->api('uuid_park', [$uuid]);
    }

    /**
     * Get active channels
     * 
     * @return string
     */
    public function getChannels(): string
    {
        return $this->api('show', ['channels']);
    }

    /**
     * Get channel information
     * 
     * @param string $uuid Channel UUID
     * @return string
     */
    public function getChannel(string $uuid): string
    {
        return $this->api('uuid_dump', [$uuid]);
    }

    /**
     * Set a channel variable
     * 
     * @param string $uuid Channel UUID
     * @param string $variable Variable name
     * @param string $value Variable value
     * @return string
     */
    public function setVariable(string $uuid, string $variable, string $value): string
    {
        return $this->api('uuid_setvar', [$uuid, $variable, $value]);
    }

    /**
     * Get a channel variable
     * 
     * @param string $uuid Channel UUID
     * @param string $variable Variable name
     * @return string
     */
    public function getVariable(string $uuid, string $variable): string
    {
        return $this->api('uuid_getvar', [$uuid, $variable]);
    }

    /**
     * Get system status
     * 
     * @return string
     */
    public function status(): string
    {
        return $this->api('status');
    }

    /**
     * Show active calls
     * 
     * @return string
     */
    public function showCalls(): string
    {
        return $this->api('show', ['calls']);
    }

    /**
     * Show SIP registrations
     * 
     * @return string
     */
    public function showRegistrations(): string
    {
        return $this->api('show', ['registrations']);
    }

    /**
     * Reload XML configuration
     * 
     * @return string
     */
    public function reloadXml(): string
    {
        return $this->api('reloadxml');
    }

    /**
     * Reload a specific module
     * 
     * @param string $module Module name
     * @return string
     */
    public function reloadModule(string $module): string
    {
        return $this->api('reload', [$module]);
    }

    /**
     * Reload ACL
     * 
     * @return string
     */
    public function reloadAcl(): string
    {
        return $this->api('reloadacl');
    }

    /**
     * Execute FreeSwitch control command
     * 
     * @param string $command Control command
     * @param array $args Arguments
     * @return string
     */
    public function fsctl(string $command, array $args = []): string
    {
        return $this->api('fsctl', array_merge([$command], $args));
    }

    /**
     * Subscribe to events
     * 
     * @param array $events Event types to subscribe to
     * @return bool
     */
    public function subscribeEvents(array $events): bool
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $eventString = implode(' ', $events);
        $this->sendCommand("event plain {$eventString}");
        $response = $this->readResponse();

        if (str_contains($response, 'Reply-Text: +OK')) {
            $this->subscribedEvents = array_merge($this->subscribedEvents, $events);
            Log::info('Subscribed to FreeSwitch events', ['events' => $events]);
            return true;
        }

        Log::error('Failed to subscribe to events', ['response' => $response]);
        return false;
    }

    /**
     * Read an event from the socket
     * 
     * @param int $timeout Timeout in seconds
     * @return array|null Event data or null if no event
     */
    public function readEvent(int $timeout = 1): ?array
    {
        if (!$this->isConnected()) {
            return null;
        }

        $response = $this->readResponse($timeout);
        
        if (empty($response) || !str_contains($response, 'Event-Name:')) {
            return null;
        }

        return $this->parseEvent($response);
    }

    /**
     * Parse event data
     * 
     * @param string $eventData Raw event data
     * @return array Parsed event
     */
    protected function parseEvent(string $eventData): array
    {
        $event = [];
        $lines = explode("\n", $eventData);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);
                $event[trim($key)] = trim($value);
            }
        }

        return $event;
    }

    /**
     * Get list of subscribed events
     * 
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return $this->subscribedEvents;
    }

    /**
     * Destructor - ensure socket is closed
     */
    public function __destruct()
    {
        $this->disconnect();
    }
}
