<?php

namespace Noardcode\LaravelUptimeMonitor\Console\Commands\UptimeMonitor;

use Exception;
use Illuminate\Console\Command;
use Noardcode\LaravelUptimeMonitor\Models\Monitor;
use Noardcode\LaravelUptimeMonitor\Repositories\Monitors;

/**
 * Class DisableMonitor
 * @package Noardcode\LaravelUptimeMonitor\Console\Commands
 */
class Disable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:disable
                            {id : The ID of the monitor to disable.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable the monitor with the given ID.';

    /**
     * @var Monitors
     */
    protected Monitors $monitors;

    /**
     * Create a new command instance.
     *
     * @param Monitors $monitors
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param Monitors $monitors
     * @return int
     * @throws Exception
     */
    public function handle(Monitors $monitors)
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

        $monitors->save([
            'enabled' => false
        ], $monitor);

        $this->info('Monitoring is disabled.');

        return 0;
    }
}
