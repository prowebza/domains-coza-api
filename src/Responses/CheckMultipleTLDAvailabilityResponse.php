<?php

namespace Balfour\DomainsResellerAPI\Responses;

use UnexpectedValueException;

class CheckMultipleTLDAvailabilityResponse extends BaseResponse
{
    /**
     * @return string
     */
    public function getSLD(): string
    {
        return $this->json['strSLD'];
    }

    /**
     * @return mixed[]
     */
    public function getTLDs(): array
    {
        $tlds = [];

        $sld = $this->getSLD();

        foreach ($this->json['arrTLDs'] as $tld => $availability) {
            $tlds[$tld] = [
                'tld' => $tld,
                'domain' => $sld . '.' . $tld,
                'is_available' => $availability['isAvailable'] === 'true',
                'uses_epp_key' => $availability['usesEppKey'] === 'true',
                'is_premium' => ($availability['isPremium'] ?? 'false') === 'true',
            ];
        }

        return $tlds;
    }

    /**
     * @return mixed[]
     */
    public function getAvailableTLDs(): array
    {
        return array_filter($this->getTLDs(), function (array $tld) {
            return $tld['is_available'];
        });
    }

    /**
     * @return mixed[]
     */
    public function getTakenTLDs(): array
    {
        return array_filter($this->getTLDs(), function (array $tld) {
            return !$tld['is_available'];
        });
    }

    /**
     * @param string $tld
     * @return mixed[]
     */
    public function getTLD(string $tld): array
    {
        $tld = strtolower($tld);

        $tlds = $this->getTLDs();

        if (!isset($tlds[$tld])) {
            throw new UnexpectedValueException(sprintf('The TLD %s is not supported.', $tld));
        }

        return $tlds[$tld];
    }

    /**
     * @param string $tld
     * @return bool
     */
    public function isTLDAvailable(string $tld): bool
    {
        $tld = $this->getTLD($tld);
        return $tld['is_available'];
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
            'sld' => $this->getSLD(),
            'tlds' => $this->getTLDs(),
        ];
    }
}
