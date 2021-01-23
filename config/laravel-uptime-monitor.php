<?php

/**
 * Configuration for Laravel Uptime Monitor.
 */
return [
    /**
     * The time in minutes between now and the last uptime check.
     * When this time has passed a new uptime check will be performed.
     */
    'interval' => env('LARAVEL_UPTIME_MONITOR_INTERVAL', 5),

    /**
     * The amount of concurrent HTTP requests that will be performed by the client.
     */
    'concurrency' => env('LARAVEL_UPTIME_MONITOR_CONCURRENCY', 10),

    /**
     * Guzzle HTTP client configuration.
     */
    'client' => [

        /**
         * When redirects are not allowed the client will return a 302 status code.
         * If this setting is enabled the client will return a 200 status code from the website redirecting to.
         */
        'allow_redirects' => env('LARAVEL_UPTIME_MONITOR_CLIENT_ALLOW_REDIRECTS', false),

        /**
         * Float describing the number of seconds to wait while trying to connect to a server.
         * Use 0 to wait indefinitely (the default behavior).
         */
        'connect_timeout' => env('LARAVEL_UPTIME_MONITOR_CLIENT_CONNECT_TIMEOUT', 0.5),

        /**
         * Associative array of headers to add to the request. Each key is the name of a header, and each value
         * is a string or array of strings representing the header field values.
         */
        'headers' => [
            'User-Agent' => env('LARAVEL_UPTIME_MONITOR_CLIENT_USER_AGENT', 'LaravelUptimeMonitor/1.0'),
        ],

        /**
         * Whether to throw or not throw exceptions on HTTP protocol errors (4xx and 5xx responses).
         */
        'http_errors' => env('LARAVEL_UPTIME_MONITOR_CLIENT_HTTP_ERRORS', false),

        /**
         * Float to indicate the total timeout of the request in seconds.
         * Use 0 to wait indefinitely.
         */
        'timeout' => env('LARAVEL_UPTIME_MONITOR_CLIENT_TIMEOUT', 5.00),
    ]
];
