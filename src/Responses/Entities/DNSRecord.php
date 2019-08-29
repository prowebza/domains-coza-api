<?php

namespace Balfour\DomainsResellerAPI\Responses\Entities;

use Badcow\DNS\ResourceRecord;

class DNSRecord extends BaseEntity
{
    /**
     * @var ResourceRecord
     */
    protected $resourceRecord;

    /**
     * @param array $attributes
     * @param ResourceRecord $resourceRecord
     */
    public function __construct(array $attributes, ResourceRecord $resourceRecord)
    {
        parent::__construct($attributes);

        $this->resourceRecord = $resourceRecord;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->attributes['type'];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->attributes['name'];
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->attributes['content'];
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->attributes['prio'];
    }

    /**
     * @return int
     */
    public function getTTL(): int
    {
        return $this->attributes['ttl'];
    }

    /**
     * @return ResourceRecord
     */
    public function getResourceRecord(): ResourceRecord
    {
        return $this->resourceRecord;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'content' => $this->getContent(),
            'priority' => $this->getPriority(),
            'ttl' => $this->getTTL(),
        ];
    }
}
