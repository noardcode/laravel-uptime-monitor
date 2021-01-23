# NoardCode Laravel Uptime Monitor
The NoardCode Laravel Uptime Monitor package provides a simple tool for monitoring 
the status of any number of URLs. Add URLs to the monitoring queue and receive
events when the URL is checked. The package uses concurrent requests to speed up
the monitoring.

### Installation
This package can be installed as every other composer dependency.

```shell script
composer require noardcode/uptime-monitor
```

Publish the package files to your project.

```shell script
php artisan vendor:publish
```
> **_NOTE:_**  Don't forget to add the published files to your Git repository.

Migrate the database.

```
php artisan migrate
```

Add the monitoring console command to your /app/Console/Kernel.php and make
sure it gets called every minute (request interval can be configured in the
configuration file).

```
protected function schedule(Schedule $schedule)
{
    $schedule->command('uptime-monitor:run')->everyMinute();
}
```

### Configuration
After publishing you'll find the file *uptime-monitor.php* in your default
Laravel config directory.

Within this configuration file you can set a number of Guzzle options,
determine the request interval per URL and the amount of concurrent requests. 

### Events
The package will dispatch several events which you may use in your application.
These events are:

* Noardcode\UptimeMonitor\Events\MonitorAvailable (the URL is available)
* Noardcode\UptimeMonitor\Events\MonitorUnavailable (the URL is unavailable)
* Noardcode\UptimeMonitor\Events\MonitorRestored (the URL is restored after downtime)

The events can be catched by your Laravel application by adding them to your
/app/Providers/EventServiceProvider.php file.

```
protected $listen = [
    MonitorAvailable::class => [
        TestListener::class, // Replace with your own listener.
    ]
];
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Contributions are **welcome** and will be fully **credited**. We accept contributions via Pull Requests on [Github](https://github.com/noardcode/laravel-uptime-monitor).

### Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - The easiest way to apply the conventions is to install [PHP Code Sniffer](http://pear.php.net/package/PHP_CodeSniffer).
- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.
- **Create feature branches** - Don't ask us to pull from your master branch.
- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

