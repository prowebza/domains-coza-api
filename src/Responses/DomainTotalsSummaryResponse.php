<?php

namespace Balfour\DomainsResellerAPI\Responses;

class DomainTotalsSummaryResponse extends BaseResponse
{
    /**
     * @return int[]
     */
    public function getSummary(): array
    {
        return [
            'total' => $this->getTotalDomains(),
            'transfers_in' => $this->getTotalTransfersIn(),
            'transfers_out' => $this->getTotalTransfersOut(),
            'pending_updates' => $this->getTotalPendingUpdates(),
            'premium_dns' => $this->getTotalPremiumDNS(),
            'registered_1day' => $this->getTotalRegistered1Day(),
            'registered_7days' => $this->getTotalRegistered7Days(),
            'registered_30days' => $this->getTotalRegistered30Days(),
            'expiring_1day' => $this->getTotalExpiring1Day(),
            'expiring_7days' => $this->getTotalExpiring7Days(),
            'expiring_14days' => $this->getTotalExpiring14Days(),
            'expiring_30days' => $this->getTotalExpiring30Days(),
            'expiring_60days' => $this->getTotalExpiring60Days(),
            'expiring_90days' => $this->getTotalExpiring90Days(),
            'expired' => $this->getTotalExpired(),
            'in_redemption' => $this->getTotalInRedemption(),
        ];
    }

    /**
     * @return int
     */
    public function getTotalDomains(): int
    {
        return $this->json['arrTotals']['intTotalDomains'];
    }

    /**
     * @return int
     */
    public function getTotalTransfersIn(): int
    {
        return $this->json['arrTotals']['intTotalTransfersIn'];
    }

    /**
     * @return int
     */
    public function getTotalTransfersOut(): int
    {
        return $this->json['arrTotals']['intTotalTransfersOut'];
    }

    /**
     * @return int
     */
    public function getTotalPendingUpdates(): int
    {
        return $this->json['arrTotals']['intTotalPendingUpdates'];
    }

    /**
     * @return int
     */
    public function getTotalPremiumDNS(): int
    {
        return $this->json['arrTotals']['intTotalPremiumDNS'];
    }

    /**
     * @return int
     */
    public function getTotalRegistered1Day(): int
    {
        return $this->json['arrTotals']['intTotalReg1'];
    }

    /**
     * @return int
     */
    public function getTotalRegistered7Days(): int
    {
        return $this->json['arrTotals']['intTotalReg7'];
    }

    /**
     * @return int
     */
    public function getTotalRegistered30Days(): int
    {
        return $this->json['arrTotals']['intTotalReg30'];
    }

    /**
     * @return int
     */
    public function getTotalExpiring1Day(): int
    {
        return $this->json['arrTotals']['intTotalExpiring1'];
    }

    /**
     * @return int
     */
    public function getTotalExpiring7Days(): int
    {
        return $this->json['arrTotals']['intTotalExpiring7'];
    }

    /**
     * @return int
     */
    public function getTotalExpiring14Days(): int
    {
        return $this->json['arrTotals']['intTotalExpiring14'];
    }

    /**
     * @return int
     */
    public function getTotalExpiring30Days(): int
    {
        return $this->json['arrTotals']['intTotalExpiring30'];
    }

    /**
     * @return int
     */
    public function getTotalExpiring60Days(): int
    {
        return $this->json['arrTotals']['intTotalExpiring60'];
    }

    /**
     * @return int
     */
    public function getTotalExpiring90Days(): int
    {
        return $this->json['arrTotals']['intTotalExpiring90'];
    }

    /**
     * @return int
     */
    public function getTotalExpired(): int
    {
        return $this->json['arrTotals']['intTotalExpired'];
    }

    /**
     * @return int
     */
    public function getTotalInRedemption(): int
    {
        return $this->json['arrTotals']['intTotalInRedemption'];
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
            'summary' => $this->getSummary(),
        ];
    }
}
