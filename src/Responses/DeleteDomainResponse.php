<?php

namespace Balfour\DomainsResellerAPI\Responses;

class DeleteDomainResponse extends BaseResponse
{
    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'return_code' => $this->getReturnCode(),
            'uuid' => $this->getUUID(),
            'message' => $this->getMessage(),
        ];
    }
}
