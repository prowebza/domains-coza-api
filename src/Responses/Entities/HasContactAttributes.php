<?php

namespace Balfour\DomainsResellerAPI\Responses\Entities;

trait HasContactAttributes
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->attributes['strContactName'];
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->attributes['strContactEmail'];
    }

    /**
     * @return string
     */
    public function getContactNumber(): string
    {
        return $this->attributes['strContactNumber'];
    }

    /**
     * @return string
     */
    public function getFaxNumber(): string
    {
        return $this->attributes['strContactFax'];
    }

    /**
     * @return mixed[]
     */
    public function getAddress(): array
    {
        $parts = $this->attributes['strContactAddress'];
        $parts = array_filter($parts); // filter out empty rows
        return array_values($parts);
    }

    /**
     * @return string
     */
    public function getAddressLine1(): string
    {
        $address = $this->getAddress();
        return $address[0];
    }

    /**
     * @return string|null
     */
    public function getAddressLine2(): ?string
    {
        $address = $this->getAddress();
        return $address[1] ?? null;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        // handle inconsistency in api
        if (isset($this->attributes['strContactPostalCode'])) {
            return $this->attributes['strContactPostalCode'];
        } elseif (isset($this->attributes['strPostalCode'])) {
            return $this->attributes['strPostalCode'];
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        // handle inconsistency in api
        if (isset($this->attributes['strContactCountry'])) {
            return $this->attributes['strContactCountry'];
        } elseif (isset($this->attributes['strCountry'])) {
            return $this->attributes['strCountry'];
        } else {
            return '';
        }
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->attributes['strContactCompany'];
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        // handle inconsistency in api
        if (isset($this->attributes['strContactCity'])) {
            return $this->attributes['strContactCity'];
        } elseif (isset($this->attributes['strCity'])) {
            return $this->attributes['strCity'];
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getProvince(): string
    {
        // handle inconsistency in api
        if (isset($this->attributes['strContactProvince'])) {
            return $this->attributes['strContactProvince'];
        } elseif (isset($this->attributes['strProvince'])) {
            return $this->attributes['strProvince'];
        } else {
            return '';
        }
    }
}
