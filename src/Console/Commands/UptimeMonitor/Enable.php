<?php

namespace Noardcode\LaravelUptimeMonitor\Console\Commands\UptimeMonitor;

use Exception;
use Illuminate\Console\Command;
use Noardcode\LaravelUptimeMonitor\Models\Monitor;
use Noardcode\LaravelUptimeMonitor\Repositories\Monitors;

/**
 * Class EnableMonitor
 * @package Noardcode\LaravelUptimeMonitor\Console\Commands
 */
class Enable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:enable
                            {id : The ID of the monitor to enable.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable the monitor with the given ID.';

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
            'enabled' => true
        ], $monitor);

        $this->info('Monitoring is enabled.');

        return 0;
    }
}
