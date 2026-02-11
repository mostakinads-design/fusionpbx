<?php

namespace App\Providers;

use App\Services\FreeSwitchEslService;
use App\Services\FreeSwitchEventSubscriber;
use Illuminate\Support\ServiceProvider;

/**
 * FreeSwitch Service Provider
 * 
 * Registers FreeSwitch services with Laravel container
 */
class FreeSwitchServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        // Register ESL service as singleton
        $this->app->singleton(FreeSwitchEslService::class, function ($app) {
            return new FreeSwitchEslService();
        });

        // Register event subscriber
        $this->app->singleton(FreeSwitchEventSubscriber::class, function ($app) {
            return new FreeSwitchEventSubscriber(
                $app->make(FreeSwitchEslService::class)
            );
        });

        // Register alias for easy access
        $this->app->alias(FreeSwitchEslService::class, 'freeswitch.esl');
        $this->app->alias(FreeSwitchEventSubscriber::class, 'freeswitch.events');
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        // Publish config if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/freeswitch.php' => config_path('freeswitch.php'),
            ], 'freeswitch-config');
        }
    }

    /**
     * Get the services provided
     */
    public function provides(): array
    {
        return [
            FreeSwitchEslService::class,
            FreeSwitchEventSubscriber::class,
            'freeswitch.esl',
            'freeswitch.events',
        ];
    }
}
