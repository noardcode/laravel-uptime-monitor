<?php

namespace Noardcode\LaravelUptimeMonitor\Console\Commands\UptimeMonitor;

use Illuminate\Console\Command;
use Noardcode\LaravelUptimeMonitor\Models\Monitor;
use Noardcode\LaravelUptimeMonitor\Repositories\Monitors;

/**
 * Class ListMonitors
 * @package Noardcode\LaravelUptimeMonitor\Console\Commands
 */
class Show extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all monitors.';

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
     * @return int
     */
    public function handle()
    {
        $monitors = Monitor::query()
            ->select('id', 'url', 'enabled', 'status')
            ->get()
            ->toArray();

        $this->table(['#', 'URL', 'Enabled', 'Status'], $monitors, 'symfony-style-guide');
    }
}
