<?php

namespace Noardcode\LaravelUptimeMonitor\Repositories;

use Noardcode\LaravelUptimeMonitor\Models\Monitor;

/**
 * Class Projects
 * @package Noardcode\PublisherProjects\Repositories
 */
class Monitors extends Repository
{
    /**
     * Attractions constructor.
     * @param Monitor $model
     */
    public function __construct(Monitor $model)
    {
        parent::__construct($model);
    }
}
