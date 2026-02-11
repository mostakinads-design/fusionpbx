<?php

namespace App\Console\Commands;

use App\Services\FreeSwitchEventSubscriber;
use Illuminate\Console\Command;

/**
 * Command to listen for FreeSwitch events
 */
class FreeSwitchListenCommand extends Command
{
    /**
     * The name and signature of the console command
     */
    protected $signature = 'freeswitch:listen
                            {--events=* : Specific events to listen for}';

    /**
     * The console command description
     */
    protected $description = 'Listen for FreeSwitch events and dispatch Laravel events';

    /**
     * Execute the console command
     */
    public function handle(FreeSwitchEventSubscriber $subscriber): int
    {
        $this->info('Starting FreeSwitch event listener...');

        $events = $this->option('events');
        
        if (empty($events)) {
            $events = null; // Use config defaults
            $this->info('Using events from configuration');
        } else {
            $this->info('Listening for events: ' . implode(', ', $events));
        }

        try {
            // Register signal handlers for graceful shutdown
            if (extension_loaded('pcntl')) {
                pcntl_async_signals(true);
                pcntl_signal(SIGTERM, function () use ($subscriber) {
                    $this->info('Received SIGTERM, stopping...');
                    $subscriber->stop();
                });
                pcntl_signal(SIGINT, function () use ($subscriber) {
                    $this->info('Received SIGINT, stopping...');
                    $subscriber->stop();
                });
            }

            $subscriber->listen($events);
            
            $this->info('Event listener stopped gracefully');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Event listener failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
