<?php

namespace Balfour\DomainsResellerAPI\Responses;

class DomainEPPKeyResponse extends BaseResponse
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
    public function getEPPKey(): string
    {
        return $this->json['strEPPKey'];
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
            'epp_key' => $this->getEPPKey(),
        ];
    }
}
