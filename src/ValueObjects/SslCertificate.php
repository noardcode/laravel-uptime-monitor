<?php

namespace Noardcode\LaravelUptimeMonitor\ValueObjects;

use Carbon\Carbon;

/**
 * Class SslCertificate
 * @package Noardcode\LaravelUptimeMonitor\ValueObjects
 */
Class SslCertificate
{
    /**
     * @var array|mixed
     */
    protected array $subject;

    /**
     * @var array|mixed
     */
    protected array $issuer;

    /**
     * @var Carbon|false
     */
    protected Carbon $validFrom;

    /**
     * @var Carbon|false
     */
    protected Carbon $validTo;

    /**
     * SslCertificate constructor.
     * @param array $parsedCertificate
     */
    public function __construct(array $parsedCertificate)
    {
        $this->subject = $parsedCertificate['subject'];

        $this->issuer = $parsedCertificate['issuer'];

        $this->validFrom = Carbon::createFromFormat('ymdHis', substr($parsedCertificate['validFrom'], 0, 12));
        $this->validTo = Carbon::createFromFormat('ymdHis', substr($parsedCertificate['validTo'], 0, 12));
    }

    /**
     * @return string
     */
    public function getSubjectCommonName(): string
    {
        return $this->subject['CN'];
    }

    /**
     * @return string
     */
    public function getIssuerCommonName(): string
    {
        return $this->issuer['O'];
    }

    /**
     * @return Carbon
     */
    public function getValidFrom(): Carbon
    {
        return $this->validFrom;
    }

    /**
     * @return Carbon
     */
    public function getValidTo(): Carbon
    {
        return $this->validTo;
    }
}
