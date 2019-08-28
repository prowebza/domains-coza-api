<?php

namespace Balfour\DomainsResellerAPI\Responses\Entities;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class Domain extends BaseEntity
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->attributes['strDomainName'];
    }

    /**
     * @return string
     */
    public function getContactName(): string
    {
        return $this->attributes['contactName'];
    }

    /**
     * @return string
     */
    public function getContactID(): string
    {
        return $this->attributes['strContactID'];
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->attributes['status'];
    }

    /**
     * @return string
     */
    public function getEPPStatus(): string
    {
        return $this->attributes['eppStatus'];
    }

    /**
     * @return bool
     */
    public function isPremiumDNSEnabled()
    {
        return $this->attributes['strDns'] === '1';
    }

    /**
     * @return CarbonInterface
     */
    public function getCreationDate(): CarbonInterface
    {
        return Carbon::createFromTimestamp($this->attributes['createdDate']);
    }

    /**
     * @return CarbonInterface
     */
    public function getExpiryDate(): CarbonInterface
    {
        return Carbon::createFromTimestamp($this->attributes['expiryDate']);
    }

    /**
     * @return bool
     */
    public function isAutoRenewEnabled(): bool
    {
        return $this->attributes['autorenew'] === 1;
    }

    /**
     * @return string
     */
    public function getExternalRef(): string
    {
        return $this->attributes['externalRef'];
    }

    /**
     * @return string[]
     */
    public function getNameservers(): array
    {
        return $this->attributes['nameservers'];
    }

    /**
     * @param string $nameserver
     * @return bool
     */
    public function hasNameserver(string $nameserver): bool
    {
        $nameserver = strtolower($nameserver);
        $nameservers = array_map('strtolower', $this->getNameservers());
        return in_array($nameserver, $nameservers);
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'contact_name' => $this->getContactName(),
            'contact_id' => $this->getContactID(),
            'status' => $this->getStatus(),
            'epp_status' => $this->getEPPStatus(),
            'is_premium_dns_enabled' => $this->isPremiumDNSEnabled(),
            'creation_date' => $this->getCreationDate()->toIso8601String(),
            'expiry_date' => $this->getExpiryDate()->toIso8601String(),
            'is_autorenew_enabled' => $this->isAutoRenewEnabled(),
            'nameservers' => $this->getNameservers(),
        ];
    }
}
