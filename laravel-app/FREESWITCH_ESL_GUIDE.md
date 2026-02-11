# FreeSwitch ESL Service Guide

## Overview

The FreeSwitch ESL (Event Socket Library) service provides a complete interface to interact with FreeSwitch PBX from Laravel applications.

## Features

✅ **Connection Management**
- Automatic connection and authentication
- Connection pooling ready
- Automatic reconnection on failure
- Non-blocking I/O

✅ **Call Control**
- Originate calls
- Hangup calls
- Answer calls
- Bridge channels
- Transfer calls
- Park calls

✅ **Channel Management**
- Get active channels
- Set/get channel variables
- Query channel information

✅ **System Management**
- Show system status
- Show active calls
- Show SIP registrations
- Reload XML configuration
- Reload modules
- Reload ACL

✅ **Event Handling**
- Subscribe to FreeSwitch events
- Automatic event parsing
- Laravel event dispatching
- CDR creation from events

## Installation

### 1. Service Provider

The FreeSwitchServiceProvider is automatically registered in `bootstrap/providers.php`.

### 2. Configuration

Configure FreeSwitch connection in `.env`:

```env
FREESWITCH_ESL_HOST=127.0.0.1
FREESWITCH_ESL_PORT=8021
FREESWITCH_ESL_PASSWORD=ClueCon
FREESWITCH_ESL_TIMEOUT=10
```

### 3. Events Configuration

Events are configured in `config/freeswitch.php`:

```php
'events' => [
    'enabled' => true,
    'subscribe_to' => [
        'CHANNEL_CREATE',
        'CHANNEL_ANSWER',
        'CHANNEL_HANGUP',
        'CHANNEL_HANGUP_COMPLETE',
        // ... more events
    ],
],
```

## Usage

### Using the Service Directly

```php
use App\Services\FreeSwitchEslService;

$esl = app(FreeSwitchEslService::class);

// Connect to FreeSwitch
$esl->connect();

// Originate a call
$result = $esl->originate(
    'user/1001',           // Destination
    '9999',                // Extension to execute
    'XML',                 // Dialplan
    '5551234',             // Caller ID number
    'Test Call',           // Caller ID name
    ['var1' => 'value1']   // Channel variables
);

// Show active calls
$calls = $esl->showCalls();

// Get system status
$status = $esl->status();

// Disconnect
$esl->disconnect();
```

### Using the Facade

```php
use App\Facades\FreeSwitch;

// Connect and originate call
FreeSwitch::connect();
$result = FreeSwitch::originate('user/1001', '9999');

// Show registrations
$registrations = FreeSwitch::showRegistrations();

// Reload XML
FreeSwitch::reloadXml();
```

### Call Control Methods

```php
// Hangup a call
FreeSwitch::hangup($uuid, 'NORMAL_CLEARING');

// Answer a call
FreeSwitch::answer($uuid);

// Bridge two calls
FreeSwitch::bridge($uuid1, $uuid2);

// Transfer a call
FreeSwitch::transfer($uuid, '1002', 'XML', 'default');

// Park a call
FreeSwitch::park($uuid);
```

### Channel Management

```php
// Get all channels
$channels = FreeSwitch::getChannels();

// Get specific channel
$channel = FreeSwitch::getChannel($uuid);

// Set channel variable
FreeSwitch::setVariable($uuid, 'my_var', 'my_value');

// Get channel variable
$value = FreeSwitch::getVariable($uuid, 'my_var');
```

### Module Management

```php
// Reload XML configuration
FreeSwitch::reloadXml();

// Reload specific module
FreeSwitch::reloadModule('mod_sofia');

// Reload ACL
FreeSwitch::reloadAcl();

// Execute fsctl command
FreeSwitch::fsctl('shutdown', ['elegant']);
```

## Event Handling

### Subscribe to Events

```php
use App\Services\FreeSwitchEslService;

$esl = app(FreeSwitchEslService::class);
$esl->connect();

// Subscribe to specific events
$esl->subscribeEvents([
    'CHANNEL_CREATE',
    'CHANNEL_ANSWER',
    'CHANNEL_HANGUP'
]);

// Read events in a loop
while ($event = $esl->readEvent(1)) {
    $eventName = $event['Event-Name'] ?? 'unknown';
    $uuid = $event['Unique-ID'] ?? 'unknown';
    
    echo "Event: {$eventName}, UUID: {$uuid}\n";
}
```

### Using Event Subscriber

```php
use App\Services\FreeSwitchEventSubscriber;

$subscriber = app(FreeSwitchEventSubscriber::class);

// Start listening (blocking)
$subscriber->listen();

// Or listen to specific events
$subscriber->listen(['CHANNEL_CREATE', 'CHANNEL_ANSWER']);
```

### Laravel Events

The event subscriber automatically dispatches Laravel events:

```php
use App\Events\CallCreated;
use App\Events\CallAnswered;
use App\Events\CallHangup;

// Listen to Laravel events
Event::listen(CallCreated::class, function (CallCreated $event) {
    $uuid = $event->getChannelUuid();
    $caller = $event->getCallerIdNumber();
    $destination = $event->getDestinationNumber();
    
    // Handle new call
});

Event::listen(CallAnswered::class, function (CallAnswered $event) {
    // Handle answered call
});

Event::listen(CallHangup::class, function (CallHangup $event) {
    $cause = $event->getHangupCause();
    $duration = $event->getDuration();
    
    // Handle hangup
});
```

## Console Commands

### Listen for Events

Start the event listener:

```bash
php artisan freeswitch:listen

# Listen to specific events
php artisan freeswitch:listen --events=CHANNEL_CREATE --events=CHANNEL_ANSWER
```

### Reload Configuration

```bash
# Reload XML (default)
php artisan freeswitch:reload

# Reload XML explicitly
php artisan freeswitch:reload --xml

# Reload specific module
php artisan freeswitch:reload --module=mod_sofia

# Reload ACL
php artisan freeswitch:reload --acl
```

### Show Status

```bash
# Show system status
php artisan freeswitch:status

# Show active calls
php artisan freeswitch:status --calls

# Show channels
php artisan freeswitch:status --channels

# Show registrations
php artisan freeswitch:status --registrations
```

## Advanced Usage

### Background API Commands

For long-running commands, use bgapi:

```php
$jobUuid = FreeSwitch::bgapi('originate', [
    'user/1001',
    '&park()'
]);

// Job UUID can be used to check job status later
```

### Connection Management

```php
$esl = app(FreeSwitchEslService::class);

// Check if connected
if (!$esl->isConnected()) {
    $esl->connect();
}

// Manual disconnect
$esl->disconnect();
```

### Error Handling

```php
use App\Exceptions\EslConnectionException;
use App\Exceptions\EslCommandException;
use App\Exceptions\EslAuthenticationException;

try {
    FreeSwitch::connect();
    $result = FreeSwitch::originate('user/1001', '9999');
} catch (EslConnectionException $e) {
    Log::error('Connection failed: ' . $e->getMessage());
} catch (EslAuthenticationException $e) {
    Log::error('Authentication failed: ' . $e->getMessage());
} catch (EslCommandException $e) {
    Log::error('Command failed: ' . $e->getMessage());
}
```

## Testing

### Unit Tests

```php
use App\Services\FreeSwitchEslService;
use Tests\TestCase;

class FreeSwitchEslServiceTest extends TestCase
{
    public function test_can_connect()
    {
        $esl = app(FreeSwitchEslService::class);
        $this->assertTrue($esl->connect());
    }
    
    public function test_can_execute_command()
    {
        $esl = app(FreeSwitchEslService::class);
        $esl->connect();
        $result = $esl->status();
        $this->assertNotEmpty($result);
    }
}
```

### Mock Testing

For testing without FreeSwitch:

```php
$mock = Mockery::mock(FreeSwitchEslService::class);
$mock->shouldReceive('connect')->andReturn(true);
$mock->shouldReceive('status')->andReturn('UP 0 years, 0 days, 1 hours');
$this->app->instance(FreeSwitchEslService::class, $mock);
```

## Performance Tips

1. **Reuse Connections**: The service is registered as a singleton
2. **Use bgapi**: For long-running commands
3. **Event Batching**: Process events in batches
4. **Connection Pooling**: Consider implementing for high-load scenarios

## Troubleshooting

### Connection Issues

```bash
# Test connection manually
telnet 127.0.0.1 8021

# Check FreeSwitch is running
fs_cli -x "status"

# Verify ESL password
grep "password" /etc/freeswitch/autoload_configs/event_socket.conf.xml
```

### Event Listener Not Receiving Events

1. Check events are enabled in config
2. Verify network connectivity
3. Check FreeSwitch logs
4. Ensure ESL password is correct

### Authentication Failures

1. Verify password in `.env` matches FreeSwitch config
2. Check FreeSwitch event_socket.conf.xml
3. Restart FreeSwitch after config changes

## API Reference

### FreeSwitchEslService Methods

| Method | Description | Returns |
|--------|-------------|---------|
| `connect()` | Connect to FreeSwitch | bool |
| `disconnect()` | Disconnect from FreeSwitch | void |
| `isConnected()` | Check connection status | bool |
| `api($cmd, $args)` | Execute API command | string |
| `bgapi($cmd, $args)` | Execute background API | string |
| `originate(...)` | Originate new call | string |
| `hangup($uuid, $cause)` | Hangup call | string |
| `answer($uuid)` | Answer call | string |
| `bridge($uuid1, $uuid2)` | Bridge channels | string |
| `transfer($uuid, $ext)` | Transfer call | string |
| `park($uuid)` | Park call | string |
| `getChannels()` | Get active channels | string |
| `getChannel($uuid)` | Get channel info | string |
| `setVariable($uuid, $var, $val)` | Set channel variable | string |
| `getVariable($uuid, $var)` | Get channel variable | string |
| `status()` | Get system status | string |
| `showCalls()` | Show active calls | string |
| `showRegistrations()` | Show SIP registrations | string |
| `reloadXml()` | Reload XML config | string |
| `reloadModule($module)` | Reload module | string |
| `reloadAcl()` | Reload ACL | string |
| `subscribeEvents($events)` | Subscribe to events | bool |
| `readEvent($timeout)` | Read an event | array\|null |

## License

This FreeSwitch ESL implementation is part of the FusionPBX Laravel integration project.

## Support

For issues and questions:
- Check FreeSwitch logs: `/var/log/freeswitch/freeswitch.log`
- Check Laravel logs: `storage/logs/laravel.log`
- FreeSwitch documentation: https://freeswitch.org/confluence/
