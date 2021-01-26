<?php

namespace Noardcode\LaravelUptimeMonitor\Models;

use Carbon\Carbon;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\TransferStats;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Noardcode\LaravelUptimeMonitor\Events\MonitorAvailable;
use Noardcode\LaravelUptimeMonitor\Events\MonitorRestored;
use Noardcode\LaravelUptimeMonitor\Events\MonitorUnavailable;
use Noardcode\LaravelUptimeMonitor\Collections\MonitorsCollection;

/**
 * Class Monitor
 * @package Noardcode\LaravelUptimeMonitor\Models
 */
class Monitor extends Model
{
    /**
     * @var string[]
     */
    protected $guarded = ['created_at', 'updated_at', 'id'];

    /**
     * @var string[]
     */
    protected $dates = [
        'checked_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'enabled' => 'bool'
    ];

    /**
     * @param array $models
     *
     * @return MonitorsCollection
     */
    public function newCollection(array $models = []): MonitorsCollection
    {
        return new MonitorsCollection($models);
    }

    /**
     * @return HasMany
     */
    public function statistics()
    {
        return $this->hasMany(MonitorStatistic::class);
    }

    /**
     * @param Response $response
     */
    public function requestSucceeded(Response $response)
    {
        if (!($response->getStatusCode() >= 200 && $response->getStatusCode() < 400)) {
            $this->monitorUnavailable('Status code ' . $response->getStatusCode());
        }

        $this->monitorAvailable($response);
    }

    /**
     * @param ConnectException $connectException
     */
    public function requestFailed(ConnectException $connectException)
    {
        $this->monitorUnavailable($connectException->getMessage());
    }

    /**
     * @param TransferStats $stats
     */
    public function receivedStats(TransferStats $stats)
    {
        MonitorStatistic::create([
            'monitor_id' => $this->id,
            'total_time' => $stats->getHandlerStat('total_time'),
            'namelookup_time' => $stats->getHandlerStat('namelookup_time'),
            'connect_time' => $stats->getHandlerStat('connect_time'),
            'pretransfer_time' => $stats->getHandlerStat('pretransfer_time'),
            'starttransfer_time' => $stats->getHandlerStat('starttransfer_time'),
            'redirect_time' => $stats->getHandlerStat('redirect_time'),
        ]);
    }

    /**
     * @param Response $response
     */
    private function monitorAvailable(Response $response)
    {
        $restored = false;
        if ($this->status == 'down') {
            $restored = true;
        }

        $this->status = 'up';
        $this->down_reason = null;
        $this->checked_at = Carbon::now();
        $this->save();

        if ($restored === true) {
            MonitorRestored::dispatch($this, $response);
        }
        MonitorAvailable::dispatch($this, $response);
    }

    /**
     * @param string $reason
     */
    private function monitorUnavailable(string $reason)
    {
        $this->status = 'down';
        $this->down_reason = $reason;
        $this->checked_at = Carbon::now();
        $this->save();

        MonitorUnavailable::dispatch($this);
    }
}
