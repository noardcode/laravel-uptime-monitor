<?php

namespace Noardcode\LaravelUptimeMonitor\Events;

use GuzzleHttp\Psr7\Response;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Noardcode\LaravelUptimeMonitor\Models\Monitor;

/**
 * Class MonitorAvailable
 * @package Noardcode\Events
 */
class MonitorAvailable
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Monitor
     */
    public Monitor $monitor;

    /**
     * @var Response
     */
    public Response $response;

    /**
     * Create a new event instance.
     *
     * @param Monitor $monitor
     * @param Response $response
     */
    public function __construct(Monitor $monitor, Response $response)
    {
        $this->monitor = $monitor;
        $this->response = $response;
    }
}
