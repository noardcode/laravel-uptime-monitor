<?php

namespace Noardcode\LaravelUptimeMonitor\Console\Commands\Ssl;

use Illuminate\Console\Command;
use Noardcode\LaravelUptimeMonitor\Models\Monitor;
use Noardcode\LaravelUptimeMonitor\Repositories\Monitors;
use Noardcode\LaravelUptimeMonitor\Services\SslService;

/**
 * Class Check
 * @package Noardcode\LaravelUptimeMonitor\Console\Commands
 */
class Check extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:ssl-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve SSL information of the monitors.';

    /**
     * @var Monitors
     */
    protected Monitors $monitors;

    /**
     * @var SslService
     */
    protected SslService $sslService;

    /**
     * Create a new command instance.
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
        $monitors = Monitor::all();
        $monitorsToRun = $monitors->getMonitorsToRun(config('laravel-uptime-monitor.ssl-interval'), true);
        $monitorsToRun = $monitorsToRun->values();

        $sslService = new SslService();
        $sslService->getCertificate($monitorsToRun);
    }
}
