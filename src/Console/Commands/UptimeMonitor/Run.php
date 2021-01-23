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
     * @param ConcurrentRequestsService $monitorService
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

    /**
     * Retrieve the URL to monitor from either the argument or user input.
     * When retrieved, remove excess slashes at the end of the URL.
     *
     * @param string|null $url
     * @return string
     */
    private function getUrl(?string $url = null): string
    {
        if (is_null($url)) {
            $url = $this->ask('Which URL should be monitored?');
        }

        return trim($url, '/');
    }

    /**
     * Check if the given URL is valid or not.
     *
     * @param string $url
     * @return mixed
     */
    private function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * Create the monitor entry through the repository.
     *
     * @param string $url
     */
    private function createMonitor(string $url): void
    {
        try {
            $this->monitors->save([
                'url' => $url
            ]);

            $this->info('Monitor created.');
        } catch (\Exception $e) {
            if (Str::contains($e->getMessage(), 'Duplicate entry')) {
                $this->error('This URL is already being monitored.');
                return;
            }

            $this->error('Unknown error: ' . $e->getMessage());
            return;
        }
    }
}
