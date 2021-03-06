<?php

namespace Noardcode\LaravelUptimeMonitor\Console\Commands\UptimeMonitor;

use GuzzleHttp\Client;
use Noardcode\LaravelUptimeMonitor\Services\ConcurrentRequestsService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Noardcode\LaravelUptimeMonitor\Models\Monitor;
use Noardcode\LaravelUptimeMonitor\Repositories\Monitors;

/**
 * Class RunMonitor
 * @package Noardcode\LaravelUptimeMonitor\Console\Commands
 */
class Run extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start monitoring the given monitors.';

    /**
     * @var Monitors
     */
    protected Monitors $monitors;

    protected ConcurrentRequestsService $monitorService;

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
        $monitorsToRun = $monitors->getMonitorsToRun(config('laravel-uptime-monitor.interval'));
        $monitorsToRun = $monitorsToRun->values();

        $client = new Client(config('laravel-uptime-monitor.client'));

        $concurrentRequestsService = new ConcurrentRequestsService($client);
        $concurrentRequestsService->doRequests($monitorsToRun, config('laravel-uptime-monitor.concurrency'));
    }
}
