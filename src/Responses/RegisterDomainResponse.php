<?php

namespace Balfour\DomainsResellerAPI\Responses;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class RegisterDomainResponse extends BaseResponse
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
     * @return string
     */
    public function getDomain(): string
    {
        return $this->json['strDomainName'];
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
    public function getCreationDate(): CarbonInterface
    {
        return Carbon::createFromTimestamp($this->json['intCrDate']);
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
            'domain' => $this->getDomain(),
            'expiry_date' => $this->getExpiryDate()->toIso8601String(),
            'creation_date' => $this->getCreationDate()->toIso8601String(),
        ];
    }
}
