<?php

namespace Balfour\DomainsResellerAPI\Responses;

class SetDomainAutoRenewResponse extends BaseResponse
{
    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->json['strDomainName'];
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
        ];
    }
}
