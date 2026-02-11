<?php

namespace App\Console\Commands;

use App\Services\FreeSwitchEslService;
use Illuminate\Console\Command;

/**
 * Command to show FreeSwitch status
 */
class FreeSwitchStatusCommand extends Command
{
    /**
     * The name and signature of the console command
     */
    protected $signature = 'freeswitch:status
                            {--calls : Show active calls}
                            {--channels : Show active channels}
                            {--registrations : Show SIP registrations}';

    /**
     * The console command description
     */
    protected $description = 'Show FreeSwitch status and statistics';

    /**
     * Execute the console command
     */
    public function handle(FreeSwitchEslService $esl): int
    {
        try {
            $esl->connect();

            if ($this->option('calls')) {
                $this->info('Active Calls:');
                $this->line($esl->showCalls());
            } elseif ($this->option('channels')) {
                $this->info('Active Channels:');
                $this->line($esl->getChannels());
            } elseif ($this->option('registrations')) {
                $this->info('SIP Registrations:');
                $this->line($esl->showRegistrations());
            } else {
                $this->info('FreeSwitch Status:');
                $this->line($esl->status());
            }

            $esl->disconnect();
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Failed to get status: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
