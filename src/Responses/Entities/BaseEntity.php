<?php

namespace Balfour\DomainsResellerAPI\Responses\Entities;

abstract class BaseEntity
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return mixed[]
     */
    abstract public function toArray(): array;
}
