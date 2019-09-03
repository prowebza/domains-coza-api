<?php

namespace Balfour\DomainsResellerAPI\Responses;

use Balfour\DomainsResellerAPI\Responses\Entities\Contact;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class DomainInfoResponse extends BaseResponse
{
    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->json['strDomainName'];
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->json['strStatus'];
    }

    /**
     * @return string
     */
    public function getEPPStatus(): string
    {
        return $this->json['strEppStatus'];
    }

    /**
     * @return Contact[]
     */
    public function getContacts(): array
    {
        return [
            'registrant' => $this->getRegistrant(),
            'admin' => $this->getAdminContact(),
            'technical' => $this->getTechnicalContact(),
            'billing' => $this->getBillingContact(),
        ];
    }

    /**
     * @return Contact
     */
    public function getRegistrant(): Contact
    {
        return new Contact($this->json['arrRegistrant']);
    }

    /**
     * @return Contact
     */
    public function getAdminContact(): Contact
    {
        return new Contact($this->json['arrAdmin']);
    }

    /**
     * @return Contact
     */
    public function getTechnicalContact(): Contact
    {
        return new Contact($this->json['arrTech']);
    }

    /**
     * @return Contact
     */
    public function getBillingContact(): Contact
    {
        return new Contact($this->json['arrBilling']);
    }

    /**
     * @return bool
     */
    public function hasPendingUpdate(): bool
    {
        /** @var Contact $contact */
        foreach ($this->getContacts() as $contact) {
            if ($contact->hasPendingUpdate()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isPremiumDNSEnabled()
    {
        return $this->json['strDns'] === 1;
    }

    /**
     * @return string
     */
    public function getNameserverType(): string
    {
        return $this->json['strNameserverType'];
    }

    /**
     * @return string[]
     */
    public function getNameservers(): array
    {
        return $this->json['arrNameservers'];
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
     * @return bool
     */
    public function isAutoRenewEnabled(): bool
    {
        return $this->json['autorenew'] === 'true';
    }

    /**
     * @return CarbonInterface
     */
    public function getCreationDate(): CarbonInterface
    {
        return Carbon::createFromTimestamp($this->json['intCrDate']);
    }

    /**
     * @return CarbonInterface
     */
    public function getExpiryDate(): CarbonInterface
    {
        return Carbon::createFromTimestamp($this->json['intExDate']);
    }

    /**
     * @return CarbonInterface
     */
    public function getSuspensionDate(): CarbonInterface
    {
        return Carbon::createFromTimestamp($this->json['intSuspendDate']);
    }

    /**
     * @return CarbonInterface
     */
    public function getRedemptionDate(): CarbonInterface
    {
        return Carbon::createFromTimestamp($this->json['intRedemptionDate']);
    }

    /**
     * @return CarbonInterface
     */
    public function getDeletionDate(): CarbonInterface
    {
        return Carbon::createFromTimestamp($this->json['intDeleteDate']);
    }

    /**
     * @return CarbonInterface
     */
    public function getLastRenewalDate(): CarbonInterface
    {
        return Carbon::createFromTimestamp($this->json['intLastRenewedDate']);
    }

    /**
     * @return CarbonInterface
     */
    public function getLastUpdateDate(): CarbonInterface
    {
        return Carbon::createFromTimestamp($this->json['intUpDate']);
    }

    /**
     * @return CarbonInterface
     */
    public function getLastNameserverChangeDate(): CarbonInterface
    {
        return Carbon::createFromTimestamp($this->json['intNSUpDate']);
    }

    /**
     * @return string
     */
    public function getExternalRef(): string
    {
        return $this->json['externalRef'];
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'return_code' => $this->getReturnCode(),
            'uuid' => $this->getUUID(),
            'message' => $this->getMessage(),
            'domain' => $this->getDomain(),
            'status' => $this->getStatus(),
            'epp_status' => $this->getEPPStatus(),
            'contacts' => [
                'registrant' => $this->getRegistrant()->toArray(),
                'admin' => $this->getAdminContact()->toArray(),
                'technical' => $this->getTechnicalContact()->toArray(),
                'billing' => $this->getBillingContact()->toArray(),
            ],
            'has_pending_update' => $this->hasPendingUpdate(),
            'is_premium_dns_enabled' => $this->isPremiumDNSEnabled(),
            'nameserver_type' => $this->getNameserverType(),
            'nameservers' => $this->getNameservers(),
            'is_autorenew_enabled' => $this->isAutoRenewEnabled(),
            'creation_date' => $this->getCreationDate()->toIso8601String(),
            'expiry_date' => $this->getExpiryDate()->toIso8601String(),
            'suspension_date' => $this->getSuspensionDate()->toIso8601String(),
            'redemption_date' => $this->getRedemptionDate()->toIso8601String(),
            'deletion_date' => $this->getDeletionDate()->toIso8601String(),
            'last_renewal_date' => $this->getLastRenewalDate()->toIso8601String(),
            'last_update_date' => $this->getLastUpdateDate()->toIso8601String(),
            'last_nameserver_change_date' => $this->getLastNameserverChangeDate()->toIso8601String(),
            'external_ref' => $this->getExternalRef(),
        ];
    }
}
