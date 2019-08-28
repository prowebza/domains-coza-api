<?php

namespace Balfour\DomainsResellerAPI\Responses\Entities;

use Balfour\DomainsResellerAPI\RegistrantInterface;

class Contact extends BaseEntity implements RegistrantInterface
{
    use HasContactAttributes;

    /**
     * @return string
     */
    public function getID(): string
    {
        return $this->attributes['strContactID'];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->attributes['strContactType'];
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->attributes['strStatus'];
    }

    /**
     * @return string|null
     */
    public function getVerificationStatus(): ?string
    {
        return $this->attributes['strVerificationStatus'] ?? null;
    }

    /**
     * @return bool
     */
    public function hasPendingUpdate(): bool
    {
        return isset($this->attributes['arrPendingUpdate']);
    }

    /**
     * @return PendingContactUpdate|null
     */
    public function getPendingUpdate(): ?PendingContactUpdate
    {
        return $this->hasPendingUpdate() ? new PendingContactUpdate($this->attributes['arrPendingUpdate']) : null;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getID(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'contact_number' => $this->getContactNumber(),
            'fax_number' => $this->getFaxNumber(),
            'address' => $this->getAddress(),
            'postal_code' => $this->getPostalCode(),
            'country_code' => $this->getCountryCode(),
            'company' => $this->getCompany(),
            'city' => $this->getCity(),
            'province' => $this->getProvince(),
            'type' => $this->getType(),
            'status' => $this->getStatus(),
            'verification_status' => $this->getVerificationStatus(),
            'has_pending_update' => $this->hasPendingUpdate(),
            'pending_update' => $this->hasPendingUpdate() ? $this->getPendingUpdate()->toArray() : null,
        ];
    }
}
