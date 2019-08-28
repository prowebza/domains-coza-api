<?php

namespace Balfour\DomainsResellerAPI;

use Exception;
use Psr\Http\Message\ResponseInterface;

class APIException extends Exception
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var mixed[]
     */
    protected $jsonResponse;

    /**
     * @param ResponseInterface $response
     * @param array $jsonResponse
     */
    public function __construct(ResponseInterface $response, array $jsonResponse)
    {
        $this->response = $response;
        $this->jsonResponse = $jsonResponse;

        $message = sprintf('%d: %s', $jsonResponse['intReturnCode'], $jsonResponse['strMessage']);

        parent::__construct($message);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return mixed[]
     */
    public function getJSONResponse(): array
    {
        return $this->jsonResponse;
    }

    /**
     * @return int
     */
    public function getReturnCode(): int
    {
        return $this->jsonResponse['intReturnCode'];
    }
}
