<?php

namespace Noardcode\LaravelUptimeMonitor\Console\Commands\UptimeMonitor;

use Illuminate\Console\Command;
use Noardcode\LaravelUptimeMonitor\Models\Monitor;
use Noardcode\LaravelUptimeMonitor\Repositories\Monitors;

/**
 * Class DeleteMonitor
 * @package Noardcode\LaravelUptimeMonitor\Console\Commands
 */
class Delete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:delete
                            {id : The ID of the monitor to delete.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the monitor with the given ID.';

    /**
     * @var Monitors
     */
    protected Monitors $monitors;

    /**
     * Create a new command instance.
     *
     * @param Monitors $monitors
     */
    public function __construct(Monitors $monitors)
    {
        parent::__construct();

        $this->monitors = $monitors;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = $this->argument('id');
        if (!is_numeric($id)) {
            $this->error('The given ID is invalid.');
            return;
        }

        $monitor = Monitor::find($id);
        if (is_null($monitor)) {
            $this->error('The monitor is not found.');
            return;
        }

        $this->monitors->delete($monitor, function() {
            $this->info('The monitor was deleted.');
        });
    }
}
