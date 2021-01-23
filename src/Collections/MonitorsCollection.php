<?php

namespace Noardcode\LaravelUptimeMonitor\Collections;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MonitorsCollection
 * @package App\Collections
 */
class MonitorsCollection extends Collection
{
    /**
     * @param int $intervalInMinutes
     * @return MonitorsCollection
     */
    public function getMonitorsToRun(int $intervalInMinutes = 5)
    {
        return $this->filter(function ($monitor) use ($intervalInMinutes) {
            return $monitor->enabled === true
                && (
                    is_null($monitor->status) ||
                    $monitor->checked_at->diffInMinutes(Carbon::now()) >= $intervalInMinutes
                );
        });
    }
}
