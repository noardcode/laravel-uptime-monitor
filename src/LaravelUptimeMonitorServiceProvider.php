<?php

namespace Noardcode\LaravelUptimeMonitor;

use Noardcode\LaravelUptimeMonitor\Console\Commands\UptimeMonitor\Create;
use Illuminate\Support\ServiceProvider;
use Noardcode\LaravelUptimeMonitor\Console\Commands\UptimeMonitor\Delete;
use Noardcode\LaravelUptimeMonitor\Console\Commands\UptimeMonitor\Disable;
use Noardcode\LaravelUptimeMonitor\Console\Commands\UptimeMonitor\Enable;
use Noardcode\LaravelUptimeMonitor\Console\Commands\UptimeMonitor\Run;
use Noardcode\LaravelUptimeMonitor\Console\Commands\UptimeMonitor\Show;

/**
 * Class LaravelUptimeMonitorServiceProvider
 *
 * @package NoardCode\LaravelUptimeMonitor
 */
class LaravelUptimeMonitorServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel-uptime-monitor.php' => config_path('laravel-uptime-monitor.php'),
            ], 'config');

            // Migrations.
            $migrations = [
                'create_monitors_table.php',
            ];
            foreach ($migrations as $migrationFilename) {
                if (!$this->migrationFileExists($migrationFilename)) {
                    $this->publishes([
                        __DIR__ . '/../database/migrations/' . $migrationFilename => database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $migrationFilename),
                    ], 'migrations');
                }
            }
        }
    }

    /**
     *
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-uptime-monitor.php', 'laravel-uptime-monitor');

        $this->commands([
            Create::class,
            Delete::class,
            Enable::class,
            Disable::class,
            Show::class,
            Run::class
        ]);
    }

    /**
     * @param string $migrationFilename
     * @return bool
     */
    public static function migrationFileExists(string $migrationFilename): bool
    {
        $len = strlen($migrationFilename);
        foreach (glob(database_path('migrations/*.php')) as $filename) {
            if ((substr($filename, -$len) === $migrationFilename)) {
                return true;
            }
        }

        return false;
    }
}
