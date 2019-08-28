<?php

namespace Balfour\DomainsResellerAPI\Responses;

class UpdateNameserversResponse extends BaseResponse
{
    /**
     * @return string
     */
    public function getEPPMessage(): string
    {
        return $this->json['strEppMessage'];
    }

    /**
     * @return string
     */
    public function getEPPReason(): string
    {
        return $this->json['strEppReason'];
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
            'epp_reason' => $this->getEPPReason(),
        ];
    }
}
