<?php

namespace Balfour\DomainsResellerAPI\Responses;

class CancelDomainDeleteResponse extends BaseResponse
{
    /**
     * @return string
     */
    public function getEPPMessage(): string
    {
        return $this->json['strEppMessage'];
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
            'epp_message' => $this->getEPPMessage(),
        ];
    }
}
