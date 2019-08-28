<?php

namespace Balfour\DomainsResellerAPI\Responses;

use Balfour\DomainsResellerAPI\Responses\Entities\Domain;

class DomainListingResponse extends BaseResponse
{
    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->json['intTotal'];
    }

    /**
     * @return int
     */
    public function getFilteredTotal(): int
    {
        return $this->json['intFilterTotal'];
    }

    /**
     * @return int
     */
    public function getReturnedTotal(): int
    {
        return $this->json['intReturnedTotal'];
    }

    /**
     * @return Domain[]
     */
    public function getDomains(): array
    {
        return array_map(function (array $attributes) {
            return new Domain($attributes);
        }, $this->json['arrDomains']);
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
            'domains' => array_map(function (Domain $domain) {
                return $domain->toArray();
            }, $this->getDomains()),
        ];
    }
}
