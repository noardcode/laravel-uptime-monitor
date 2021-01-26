<?php

namespace Noardcode\LaravelUptimeMonitor\Services;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\TransferStats;
use Noardcode\LaravelUptimeMonitor\Collections\MonitorsCollection;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class ConcurrentRequestsService
 * @package Noardcode\LaravelUptimeMonitor\Services
 */
class ConcurrentRequestsService
{
    /**
     * @var ClientInterface
     */
    protected ClientInterface $client;

    /**
     * MonitorService constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param MonitorsCollection $monitors
     * @param int $concurrency
     */
    public function doRequests(MonitorsCollection $monitors, int $concurrency = 10)
    {
        $requests = function ($monitors) { // Generator function.
            for ($i = 0; $i < $monitors->count(); $i++) {
                yield new Request('GET', $monitors[$i]->url);
            }
        };

        $pool = new Pool($this->client, $requests($monitors), [
            'concurrency' => $concurrency,
            'fulfilled' => function ($response, $index) use ($monitors) {
                $monitors[$index]->requestSucceeded($response);
                (new ConsoleOutput())->writeln($monitors[$index]->url . ': Available');
            },
            'options' => [
                'on_stats' => function (TransferStats $stats) use ($monitors) {
                    foreach ($monitors as $monitor) {
                        if ($monitor->url == (string) $stats->getEffectiveUri()) {
                            $monitor->receivedStats($stats);
                        }
                    }
                }
            ],
            'rejected' => function ($connectException, $index) use ($monitors) {
                $monitors[$index]->requestFailed($connectException);
                (new ConsoleOutput())->writeln($monitors[$index]->url . ': Unavailable');
            },
        ]);

        // Initiate the transfers and create a promise
        $promise = $pool->promise();

        // Force the pool of requests to complete.
        $promise->wait();
    }
}
