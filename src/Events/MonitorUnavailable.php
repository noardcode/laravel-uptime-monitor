<?php

namespace Noardcode\LaravelUptimeMonitor\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Noardcode\LaravelUptimeMonitor\Models\Monitor;

/**
 * Class MonitorUnavailable
 * @package Noardcode\Events
 */
class MonitorUnavailable
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Monitor
     */
    public Monitor $monitor;

    /**
     * Create a new event instance.
     *
     * @param Monitor $monitor
     */
    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }
}
