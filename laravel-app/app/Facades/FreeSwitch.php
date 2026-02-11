<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * FreeSwitch Facade
 * 
 * Provides easy access to FreeSwitch ESL service
 * 
 * @method static bool connect()
 * @method static void disconnect()
 * @method static bool isConnected()
 * @method static string api(string $command, array $args = [])
 * @method static string bgapi(string $command, array $args = [])
 * @method static string originate(string $destination, string $extension, string $dialplan = 'XML', string $callerIdNumber = '', string $callerIdName = '', array $variables = [])
 * @method static string hangup(string $uuid, string $cause = 'NORMAL_CLEARING')
 * @method static string answer(string $uuid)
 * @method static string bridge(string $uuid1, string $uuid2)
 * @method static string transfer(string $uuid, string $extension, string $dialplan = 'XML', string $context = 'default')
 * @method static string park(string $uuid)
 * @method static string getChannels()
 * @method static string getChannel(string $uuid)
 * @method static string setVariable(string $uuid, string $variable, string $value)
 * @method static string getVariable(string $uuid, string $variable)
 * @method static string status()
 * @method static string showCalls()
 * @method static string showRegistrations()
 * @method static string reloadXml()
 * @method static string reloadModule(string $module)
 * @method static string reloadAcl()
 * @method static bool subscribeEvents(array $events)
 * @method static array|null readEvent(int $timeout = 1)
 * 
 * @see \App\Services\FreeSwitchEslService
 */
class FreeSwitch extends Facade
{
    /**
     * Get the registered name of the component
     */
    protected static function getFacadeAccessor(): string
    {
        return 'freeswitch.esl';
    }
}
