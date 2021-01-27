<?php

namespace Noardcode\LaravelUptimeMonitor\Services;

use GuzzleHttp\Psr7\Uri;
use Noardcode\LaravelUptimeMonitor\Collections\MonitorsCollection;
use Noardcode\LaravelUptimeMonitor\ValueObjects\SslCertificate;

/**
 * Class SslService
 * @package Noardcode\LaravelUptimeMonitor\Services
 */
class SslService
{
    /**
     * @param MonitorsCollection $monitors
     */
    public function getCertificate(MonitorsCollection $monitors)
    {
        foreach ($monitors as $monitor) {
            $url = new Uri($monitor->url);
            $response = $this->downloadCertificate($url);
            if (!is_null($response)) {
                $certificate = $this->parseCertificate($response);
                $monitor->certificateReceived($certificate);
            } else {
                $monitor->certificateFailed();
            }
        }
    }

    /**
     * @param Uri $url
     * @return ?array
     */
    private function downloadCertificate(Uri $url): ?array
    {
        $streamContext = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
            ],
        ]);

        $timeout = 10;

        try {
            $client = stream_socket_client(
                'ssl://' . $url->getHost() . ':443',
                $errorNumber,
                $errorDescription,
                $timeout,
                STREAM_CLIENT_CONNECT,
                $streamContext);
        } catch (\Exception $e) {
            return null;
        }

        return stream_context_get_params($client);
    }

    /**
     * @param array $certificate
     * @return SslCertificate
     */
    public function parseCertificate(array $certificate): SslCertificate
    {
        $parsedCertificate = openssl_x509_parse($certificate['options']['ssl']['peer_certificate']);

        return new SslCertificate($parsedCertificate);
    }
}
