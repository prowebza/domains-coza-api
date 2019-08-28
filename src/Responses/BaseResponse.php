<?php

namespace Balfour\DomainsResellerAPI\Responses;

abstract class BaseResponse
{
    /**
     * @var mixed[]
     */
    protected $json;

    /**
     * @param array $json
     */
    public function __construct(array $json)
    {
        $this->json = $json;
    }

    /**
     * @return mixed[]
     */
    public function getJSON(): array
    {
        return $this->json;
    }

    /**
     * @return int
     */
    public function getReturnCode(): int
    {
        return $this->json['intReturnCode'];
    }

    /**
     * @return string
     */
    public function getUUID(): string
    {
        return $this->json['strUUID'];
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->json['strMessage'];
    }

    /**
     * @return mixed[]
     */
    abstract public function toArray(): array;
}
