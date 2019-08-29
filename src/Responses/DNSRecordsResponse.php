<?php

namespace Balfour\DomainsResellerAPI\Responses;

use Badcow\DNS\ResourceRecord;
use Badcow\DNS\Zone;
use Balfour\DomainsResellerAPI\ResourceRecordParser;
use Balfour\DomainsResellerAPI\Responses\Entities\DNSRecord;
use Balfour\DomainsResellerAPI\Utils;

class DNSRecordsResponse extends BaseResponse
{
    /**
     * @var string
     */
    protected $domain;

    /**
     * @param array $json
     * @param string $domain
     */
    public function __construct(array $json, string $domain)
    {
        parent::__construct($json);

        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return DNSRecord[]
     */
    public function getRecords(): array
    {
        return array_map(function (array $attributes) {
            $resourceRecord = ResourceRecordParser::toResourceRecord($this->domain, $attributes);

            return new DNSRecord($attributes, $resourceRecord);
        }, $this->json['arrRecords']);
    }

    /**
     * @param string $type
     * @return DNSRecord[]
     */
    public function getRecordsByType(string $type): array
    {
        $type = strtoupper($type);

        return array_filter($this->getRecords(), function (DNSRecord $record) use ($type) {
            return $record->getType() === $type;
        });
    }

    /**
     * @return ResourceRecord[]
     */
    public function getResourceRecords(): array
    {
        return array_map(function (DNSRecord $record) {
            return $record->getResourceRecord();
        }, $this->getRecords());
    }

    /**
     * @return Zone
     */
    public function getZone(): Zone
    {
        $zone = new Zone(Utils::transformDomainToZone($this->domain));
        $zone->fromArray($this->getResourceRecords());
        return $zone;
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
            'records' => array_map(function (DNSRecord $record) {
                return $record->toArray();
            }, $this->getRecords()),
        ];
    }
}
