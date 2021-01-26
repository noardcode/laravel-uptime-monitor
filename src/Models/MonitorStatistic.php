<?php

namespace Noardcode\LaravelUptimeMonitor\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MonitorStatistic
 * @package Noardcode\LaravelUptimeMonitor\Models
 */
class MonitorStatistic extends Model
{
    /**
     * @var string[]
     */
    protected $guarded = ['created_at', 'updated_at', 'id'];
}
