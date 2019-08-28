<?php

namespace Balfour\DomainsResellerAPI\Responses;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class CheckDomainTransferResponse extends BaseResponse
{
    /**
     * @return mixed[]|null
     */
    protected function getTransfer(): ?array
    {
        return $this->json['arrDomains'][0] ?? null;
    }

    /**
     * @return string|null
     */
    public function getDomain(): ?string
    {
        $transfer = $this->getTransfer();

        return $transfer ? $transfer['strDomainName'] : null;
    }

    /**
     * @return CarbonInterface|null
     */
    public function getRequestDate(): ?CarbonInterface
    {
        $transfer = $this->getTransfer();

        return $transfer ? Carbon::createFromTimestamp($transfer['requestDate']) : null;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        $transfer = $this->getTransfer();

        return $transfer ? $transfer['status'] : null;
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->getStatus() === 'pending';
    }

    /**
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->getStatus() === 'Transfer Successful';
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        $requestDate = $this->getRequestDate();

        return [
            'return_code' => $this->getReturnCode(),
            'uuid' => $this->getUUID(),
            'message' => $this->getMessage(),
            'domain' => $this->getDomain(),
            'request_date' => $requestDate ? $requestDate->toIso8601String() : null,
            'status' => $this->getStatus(),
            'is_complete' => $this->isComplete(),
            'is_pending' => $this->isPending(),
        ];
    }
}
