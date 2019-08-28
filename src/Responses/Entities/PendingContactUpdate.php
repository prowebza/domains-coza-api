<?php

namespace Balfour\DomainsResellerAPI\Responses\Entities;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class PendingContactUpdate extends BaseEntity
{
    use HasContactAttributes;

    /**
     * @return CarbonInterface
     */
    public function getExpectedChangeDate(): CarbonInterface
    {
        return Carbon::parse($this->attributes['dtExpectedChangeDate']);
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
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
            'expected_change_date' => $this->getExpectedChangeDate()->toIso8601String(),
        ];
    }
}
