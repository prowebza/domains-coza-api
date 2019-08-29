<?php

namespace Balfour\DomainsResellerAPI;

use Badcow\DNS\Rdata\A;
use Badcow\DNS\Rdata\AAAA;
use Badcow\DNS\Rdata\CNAME;
use Badcow\DNS\Rdata\Factory;
use Badcow\DNS\Rdata\MX;
use Badcow\DNS\Rdata\TXT;
use Badcow\DNS\ResourceRecord;
use Exception;

abstract class ResourceRecordParser
{
    /**
     * @param ResourceRecord $record
     * @return mixed[]|null
     */
    public static function fromResourceRecord(ResourceRecord $record): ?array
    {
        // domains.co.za only supports specific types of records
        if (!static::isSupportedRecordType($record->getType())) {
            return null;
        }

        $name = static::normaliseHostFromResourceRecord($record->getName());
        $ttl = $record->getTtl() ?? 3600;
        $rdata = $record->getRdata();
        $type = $record->getType();
        $content = null;
        $priority = 0;

        switch ($type) {
            case 'AAAA':
            case 'A':
                /** @var AAAA|A $rdata */
                $content = $rdata->getAddress();
                break;
            case 'CNAME':
                /** @var CNAME $rdata */
                $content = static::normaliseHostFromResourceRecord($rdata->getTarget());
                break;
            case 'MX':
                /** @var MX $rdata */
                $content = static::normaliseHostFromResourceRecord($rdata->getExchange());
                $priority = $rdata->getPreference() ?? 5;
                break;
            case 'TXT':
                /** @var TXT $rdata */
                $content = $rdata->getText();
                break;
        }

        return [
            'prio' => $priority,
            'ttl' => $ttl,
            'type' => $type,
            'content' => $content,
            'name' => $name,
        ];
    }

    /**
     * @param ResourceRecord[] $records
     * @return mixed[]
     */
    public static function fromResourceRecords(array $records): array
    {
        $records = array_map(function (ResourceRecord $record) {
            return static::fromResourceRecord($record);
        }, $records);

        return array_filter($records);
    }

    /**
     * @param string $value
     * @return string
     */
    protected static function normaliseHostFromResourceRecord(string $value): string
    {
        // we need to manipulate host / mx exchange values
        // domains.co.za doesn't expect values in typical zone format

        // strip trailing .
        if (mb_substr($value, -1) === '.') {
            $value = mb_substr($value, 0, mb_strlen($value) - 1);
        }

        // @ becomes empty string
        if ($value === '@') {
            return '';
        }

        return $value;
    }

    /**
     * @param string $domain
     * @param mixed[] $record
     * @return ResourceRecord
     * @throws Exception
     */
    public static function toResourceRecord(string $domain, array $record): ResourceRecord
    {
        if (!static::isSupportedRecordType($record['type'])) {
            throw new Exception(sprintf('The record type "%s" is not supported.', $record['type']));
        }

        $name = static::normaliseHostForResourceRecord($domain, $record['name']);
        $ttl = $record['ttl'];
        $rdata = null;

        switch ($record['type']) {
            case 'AAAA':
                $rdata = Factory::Aaaa($record['content']);
                break;
            case 'A':
                $rdata = Factory::A($record['content']);
                break;
            case 'CNAME':
                $rdata = Factory::Cname(static::normaliseHostForResourceRecord($domain, $record['content']));
                break;
            case 'MX':
                $rdata = Factory::Mx(
                    $record['prio'],
                    static::normaliseHostForResourceRecord($domain, $record['content'])
                );
                break;
            case 'TXT':
                $rdata = Factory::txt($record['content']);
                break;
        }

        $resourceRecord = new ResourceRecord;
        $resourceRecord->setName($name);
        $resourceRecord->setRdata($rdata);
        $resourceRecord->setTtl($ttl);

        return $resourceRecord;
    }

    /**
     * @param string $domain
     * @param string $value
     * @return string
     */
    protected static function normaliseHostForResourceRecord(string $domain, string $value): string
    {
        if ($value === '' || $value === $domain) {
            return '@';
        }

        $pattern = sprintf('/^(.+)\.(%s)$/i', preg_quote($domain));

        if (preg_match($pattern, $value, $matches)) {
            // this is internal
            return $matches[1];
        }

        // this is external
        return $value . '.';
    }

    /**
     * @param string $type
     * @return bool
     */
    protected static function isSupportedRecordType(string $type): bool
    {
        return in_array($type, ['AAAA', 'A', 'CNAME', 'MX', 'TXT']);
    }
}

