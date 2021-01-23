<?php

namespace Noardcode\LaravelUptimeMonitor\Console\Commands\UptimeMonitor;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Noardcode\LaravelUptimeMonitor\Repositories\Monitors;

/**
 * Class CreateMonitor
 * @package Noardcode\LaravelUptimeMonitor\Console\Commands
 */
class Create extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:create
                            {url? : The URL of the website to monitor.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a monitor for the given URL.';

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
     */
    public function handle(Monitors $monitors)
    {
        $url = $this->getUrl($this->argument('url'));
        if (!$this->isValidUrl($url)) {
            $this->error('The input does not represent a valid URL.');
            return;
        }

        $this->createMonitor($url, $monitors);
    }

    /**
     * Retrieve the URL to monitor from either the argument or user input.
     * When retrieved, remove excess slashes at the end of the URL.
     *
     * @param string|null $url
     * @return Uri
     */
    private function getUrl(?string $url = null): Uri
    {
        if (is_null($url)) {
            $url = $this->ask('Which URL should be monitored?');
        }

        return new Uri($url);
    }

    /**
     * Check if the given URL is valid or not.
     *
     * @param Uri $url
     * @return mixed
     */
    private function isValidUrl(Uri $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * Create the monitor entry through the repository.
     *
     * @param string $url
     */
    private function createMonitor(string $url, Monitors $monitors): void
    {
        try {
            $monitors->save([
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
