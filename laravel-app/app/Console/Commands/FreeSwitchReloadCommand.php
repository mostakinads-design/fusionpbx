<?php

namespace App\Console\Commands;

use App\Services\FreeSwitchEslService;
use Illuminate\Console\Command;

/**
 * Command to reload FreeSwitch configuration
 */
class FreeSwitchReloadCommand extends Command
{
    /**
     * The name and signature of the console command
     */
    protected $signature = 'freeswitch:reload
                            {--xml : Reload XML configuration}
                            {--module= : Reload specific module}
                            {--acl : Reload ACL}';

    /**
     * The console command description
     */
    protected $description = 'Reload FreeSwitch configuration';

    /**
     * Execute the console command
     */
    public function handle(FreeSwitchEslService $esl): int
    {
        try {
            $esl->connect();

            if ($this->option('xml')) {
                $this->info('Reloading XML configuration...');
                $result = $esl->reloadXml();
                $this->info($result);
            }

            if ($module = $this->option('module')) {
                $this->info("Reloading module: {$module}...");
                $result = $esl->reloadModule($module);
                $this->info($result);
            }

            if ($this->option('acl')) {
                $this->info('Reloading ACL...');
                $result = $esl->reloadAcl();
                $this->info($result);
            }

            // If no options specified, reload XML by default
            if (!$this->option('xml') && !$this->option('module') && !$this->option('acl')) {
                $this->info('Reloading XML configuration (default)...');
                $result = $esl->reloadXml();
                $this->info($result);
            }

            $esl->disconnect();
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Failed to reload: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
