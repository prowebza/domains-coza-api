<?php

namespace Balfour\DomainsResellerAPI;

use Exception;

abstract class Utils
{
    /**
     * @param string $domain
     * @return string[]
     * @throws Exception
     */
    public static function parseDomain(string $domain): array
    {
        // split out sld (second level domain) and tld (top level domain)
        $p = mb_strpos($domain, '.');

        if ($p === false) {
            throw new Exception(sprintf('The domain %s does not contain a valid sld and tld.', $domain));
        }

        $sld = mb_substr($domain, 0, $p);
        $tld = mb_substr($domain, $p + 1);

        return [
            'fqdn' => $domain,
            'sld' => $sld,
            'tld' => $tld,
        ];
    }

    /**
     * @param string $zone
     * @return string
     */
    public static function transformZoneToDomain(string $zone): string
    {
        if (mb_substr($zone, -1) === '.') {
            $zone = mb_substr($zone, 0, mb_strlen($zone) - 1);
        }

        return $zone;
    }

    /**
     * @param string $domain
     * @return string
     */
    public static function transformDomainToZone(string $domain): string
    {
        if (mb_substr($domain, -1) !== '.') {
            $domain .= '.';
        }

        return $domain;
    }
}
