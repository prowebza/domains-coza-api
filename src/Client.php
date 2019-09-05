<?php

namespace Balfour\DomainsResellerAPI;

use Badcow\DNS\Parser\Parser as ZoneParser;
use Badcow\DNS\ResourceRecord;
use Badcow\DNS\Zone;
use Balfour\DomainsResellerAPI\Responses\CancelDomainDeleteResponse;
use Balfour\DomainsResellerAPI\Responses\CancelDomainTransferResponse;
use Balfour\DomainsResellerAPI\Responses\CancelDomainUpdateResponse;
use Balfour\DomainsResellerAPI\Responses\CheckDomainTransferResponse;
use Balfour\DomainsResellerAPI\Responses\CheckMultipleTLDAvailabilityResponse;
use Balfour\DomainsResellerAPI\Responses\DeleteDomainResponse;
use Balfour\DomainsResellerAPI\Responses\DNSRecordsResponse;
use Balfour\DomainsResellerAPI\Responses\DomainEPPKeyResponse;
use Balfour\DomainsResellerAPI\Responses\DomainInfoResponse;
use Balfour\DomainsResellerAPI\Responses\DomainListingResponse;
use Balfour\DomainsResellerAPI\Responses\DomainTotalsSummaryResponse;
use Balfour\DomainsResellerAPI\Responses\RegisterDomainResponse;
use Balfour\DomainsResellerAPI\Responses\RenewDomainResponse;
use Balfour\DomainsResellerAPI\Responses\SetDomainAutoRenewResponse;
use Balfour\DomainsResellerAPI\Responses\SuspendDomainResponse;
use Balfour\DomainsResellerAPI\Responses\UnsuspendDomainResponse;
use Balfour\DomainsResellerAPI\Responses\UpdateDNSRecordsResponse;
use Balfour\DomainsResellerAPI\Responses\UpdateDomainRegistrantResponse;
use Balfour\DomainsResellerAPI\Responses\UpdateNameserversResponse;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class Client
{
    /**
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $uri = 'https://api-v3.domains.co.za/api';

    /**
     * @var ResponseInterface|null
     */
    protected $lastResponse;

    /**
     * @param GuzzleClient $guzzle
     * @param string $apiKey
     */
    public function __construct(GuzzleClient $guzzle, string $apiKey)
    {
        $this->guzzle = $guzzle;
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $endpoint
     * @param mixed[] $params
     * @return string
     */
    protected function getBaseUri(string $endpoint, array $params = []): string
    {
        $uri = $this->uri;
        $uri = rtrim($uri, '/');
        $uri .= '/' . ltrim($endpoint, '/');

        if (count($params) > 0) {
            $uri .= '?' . http_build_query($params);
        }

        return $uri;
    }

    /**
     * @return mixed[]
     */
    protected function getDefaultRequestOptions(): array
    {
        return [
            'connect_timeout' => 2000,
            'timeout' => 6000,
        ];
    }

    /**
     * @param int $returnCode
     * @return bool
     */
    protected function isSuccessfulReturnCode(int $returnCode): bool
    {
        return in_array($returnCode, [
            ReturnCode::SUCCESSFUL,
            ReturnCode::PENDING_ACTION_SUCCESSFUL,
        ]);
    }

    /**
     * @param Request $request
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws APIException
     */
    protected function sendRequest(Request $request): array
    {
        $options = $this->getDefaultRequestOptions();
        $response = $this->guzzle->send($request, $options);
        $this->lastResponse = $response;
        $body = (string) $response->getBody();
        $json = json_decode($body, true);

        if (!$this->isSuccessfulReturnCode($json['intReturnCode'])) {
            throw new APIException($response, $json);
        }

        return $json;
    }

    /**
     * @param string $endpoint
     * @param mixed[] $payload
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws APIException
     */
    public function post(string $endpoint, array $payload = []): array
    {
        $payload['key'] = $this->apiKey;

        $body = \GuzzleHttp\Psr7\stream_for(http_build_query($payload));

        $request = new Request(
            'POST',
            $this->getBaseUri($endpoint),
            [
                'Content-type' => 'application/x-www-form-urlencoded',
            ],
            $body
        );
        return $this->sendRequest($request);
    }

    /**
     * @return ResponseInterface|null
     */
    public function getLastResponse(): ?ResponseInterface
    {
        return $this->lastResponse;
    }

    /**
     * @param string[] $nameservers
     * @return string[]
     */
    protected function normaliseNameservers(array $nameservers): array
    {
        // max of 10 nameservers supported
        $nameservers = array_splice($nameservers, 0, 10);

        // make sure we have numeric keys in sequential order
        $nameservers = array_values($nameservers);

        $return = [];

        for ($x = 0; $x < count($nameservers); $x++) {
            $return['ns' . ($x + 1)] = $nameservers[$x];
        }

        return $return;
    }

    /**
     * @param string $domain
     * @param string $registrantName
     * @param string $registrantEmail
     * @param string $registrantContactNumber
     * @param string $registrantAddress1
     * @param string|null $registrantAddress2
     * @param string $registrantPostalCode
     * @param string|null $registrantCountryCode
     * @param string|null $registrantCompany
     * @param string $registrantCity
     * @param string $registrantProvince
     * @param int $years
     * @param bool $useManagedNameservers
     * @param string[] $customNameservers
     * @param string|null $externalRef
     * @return RegisterDomainResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function registerDomain(
        string $domain,
        string $registrantName,
        string $registrantEmail,
        string $registrantContactNumber,
        string $registrantAddress1,
        ?string $registrantAddress2,
        string $registrantPostalCode,
        ?string $registrantCountryCode,
        ?string $registrantCompany,
        string $registrantCity,
        string $registrantProvince,
        int $years = 1,
        bool $useManagedNameservers = true,
        array $customNameservers = [],
        ?string $externalRef = null
    ): RegisterDomainResponse {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
            'registrantName' => $registrantName,
            'registrantEmail' => $registrantEmail,
            'registrantContactNumber' => $registrantContactNumber,
            'registrantAddress1' => $registrantAddress1,
            'registrantAddress2' => $registrantAddress2,
            'registrantPostalCode' => $registrantPostalCode,
            'registrantCountry' => $registrantCountryCode ?? 'ZA',
            'registrantCompany' => $registrantCompany,
            'registrantCity' => $registrantCity,
            'registrantProvince' => $registrantProvince,
            'period' => $years,
            'dns' => $useManagedNameservers ? 'managed' : 'custom',
            'externalRef' => $externalRef,
        ];

        if (!$useManagedNameservers) {
            $payload = array_merge($payload, $this->normaliseNameservers($customNameservers));
        }

        $response = $this->post('domain/domain/create', $payload);

        return new RegisterDomainResponse($response);
    }

    /**
     * @param string $domain
     * @param RegistrantInterface $registrant
     * @param int $years
     * @param bool $useManagedNameservers
     * @param string[] $customNameservers
     * @param string|null $externalRef
     * @return RegisterDomainResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function registerDomainForRegistrant(
        string $domain,
        RegistrantInterface $registrant,
        int $years = 1,
        bool $useManagedNameservers = true,
        array $customNameservers = [],
        ?string $externalRef = null
    ): RegisterDomainResponse {
        return $this->registerDomain(
            $domain,
            $registrant->getName(),
            $registrant->getEmail(),
            $registrant->getContactNumber(),
            $registrant->getAddressLine1(),
            $registrant->getAddressLine2(),
            $registrant->getPostalCode(),
            $registrant->getCountryCode(),
            $registrant->getCompany(),
            $registrant->getCity(),
            $registrant->getProvince(),
            $years,
            $useManagedNameservers,
            $customNameservers,
            $externalRef
        );
    }

    /**
     * @param string $domain
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function isDomainAvailable(string $domain): bool
    {
        try {
            $domain = Utils::parseDomain($domain);

            $payload = [
                'sld' => $domain['sld'],
                'tld' => $domain['tld'],
            ];

            $response = $this->post('domain/domain/check', $payload);

            return $response['isAvailable'] === 'true';
        } catch (APIException $e) {
            // return code 0 is not actually an error, it means "not available" or "failed"; which in this case,
            // means the domain isn't available
            if ($e->getReturnCode() === 0) {
                return false;
            }

            // for everything else, bubble
            throw $e;
        }
    }

    /**
     * @param string $domain
     * @return DeleteDomainResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function deleteDomain(string $domain): DeleteDomainResponse
    {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
        ];

        $response = $this->post('domain/domain/delete', $payload);

        return new DeleteDomainResponse($response);
    }

    /**
     * @param string $domain
     * @param string $registrantName
     * @param string $registrantEmail
     * @param string $registrantContactNumber
     * @param string $registrantAddress1
     * @param string|null $registrantAddress2
     * @param string $registrantPostalCode
     * @param string|null $registrantCountryCode
     * @param string|null $registrantCompany
     * @param string $registrantCity
     * @param string $registrantProvince
     * @return UpdateDomainRegistrantResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function updateDomainRegistrant(
        string $domain,
        string $registrantName,
        string $registrantEmail,
        string $registrantContactNumber,
        string $registrantAddress1,
        ?string $registrantAddress2,
        string $registrantPostalCode,
        ?string $registrantCountryCode,
        ?string $registrantCompany,
        string $registrantCity,
        string $registrantProvince
    ): UpdateDomainRegistrantResponse {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
            'contactName' => $registrantName,
            'contactEmail' => $registrantEmail,
            'contactContactNumber' => $registrantContactNumber,
            'contactAddress1' => $registrantAddress1,
            'contactAddress2' => $registrantAddress2,
            'contactPostalCode' => $registrantPostalCode,
            'contactCountry' => $registrantCountryCode ?? 'ZA',
            'contactCompany' => $registrantCompany,
            'contactCity' => $registrantCity,
            'contactProvince' => $registrantProvince,
        ];

        $response = $this->post('domain/domain/update', $payload);

        return new UpdateDomainRegistrantResponse($response);
    }

    /**
     * @param string $domain
     * @param RegistrantInterface $registrant
     * @return UpdateDomainRegistrantResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateDomainRegistrantFromRegistrant(
        string $domain,
        RegistrantInterface $registrant
    ): UpdateDomainRegistrantResponse {
        return $this->updateDomainRegistrant(
            $domain,
            $registrant->getName(),
            $registrant->getEmail(),
            $registrant->getContactNumber(),
            $registrant->getAddressLine1(),
            $registrant->getAddressLine2(),
            $registrant->getPostalCode(),
            $registrant->getCountryCode(),
            $registrant->getCompany(),
            $registrant->getCity(),
            $registrant->getProvince()
        );
    }

    /**
     * @param string $domain
     * @param int $years
     * @return RenewDomainResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function renewDomain(string $domain, int $years = 1): RenewDomainResponse
    {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
            'period' => $years,
        ];

        $response = $this->post('domain/domain/renew', $payload);

        return new RenewDomainResponse($response);
    }

    /**
     * @param string $domain
     * @param string $registrantName
     * @param string $registrantEmail
     * @param string $registrantContactNumber
     * @param string $registrantAddress1
     * @param string|null $registrantAddress2
     * @param string $registrantPostalCode
     * @param string|null $registrantCountryCode
     * @param string|null $registrantCompany
     * @param string $registrantCity
     * @param string $registrantProvince
     * @param string|null $eppKey
     * @param string $dns (keep|managed|custom)
     * @param string[] $customNameservers
     * @param string|null $externalRef
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function transferDomain(
        string $domain,
        string $registrantName,
        string $registrantEmail,
        string $registrantContactNumber,
        string $registrantAddress1,
        ?string $registrantAddress2,
        string $registrantPostalCode,
        ?string $registrantCountryCode,
        ?string $registrantCompany,
        string $registrantCity,
        string $registrantProvince,
        ?string $eppKey = null,
        string $dns = 'keep',
        array $customNameservers = [],
        ?string $externalRef = null
    ): array {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
            'registrantName' => $registrantName,
            'registrantEmail' => $registrantEmail,
            'registrantContactNumber' => $registrantContactNumber,
            'registrantAddress1' => $registrantAddress1,
            'registrantAddress2' => $registrantAddress2,
            'registrantPostalCode' => $registrantPostalCode,
            'registrantCountry' => $registrantCountryCode,
            'registrantCompany' => $registrantCompany,
            'registrantCity' => $registrantCity,
            'registrantProvince' => $registrantProvince,
            'eppKey' => $eppKey,
            'dns' => $dns,
            'externalRef' => $externalRef,
        ];

        if ($dns === 'custom') {
            $payload = array_merge($payload, $this->normaliseNameservers($customNameservers));
        }

        return $this->post('domain/domain/transfer', $payload);
    }

    /**
     * @param string $domain
     * @param RegistrantInterface $registrant
     * @param string|null $eppKey
     * @param string $dns (keep|managed|custom)
     * @param array $customNameservers
     * @param string|null $externalRef
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function transferDomainForRegistrant(
        string $domain,
        RegistrantInterface $registrant,
        ?string $eppKey = null,
        string $dns = 'keep',
        array $customNameservers = [],
        ?string $externalRef = null
    ): array {
        return $this->transferDomain(
            $domain,
            $registrant->getName(),
            $registrant->getEmail(),
            $registrant->getContactNumber(),
            $registrant->getAddressLine1(),
            $registrant->getAddressLine2(),
            $registrant->getPostalCode(),
            $registrant->getCountryCode(),
            $registrant->getCompany(),
            $registrant->getCity(),
            $registrant->getProvince(),
            $eppKey,
            $dns,
            $customNameservers,
            $externalRef
        );
    }

    /**
     * @param string $domain
     * @return SuspendDomainResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function suspendDomain(string $domain): SuspendDomainResponse
    {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
        ];

        $response = $this->post('domain/domain/suspend', $payload);

        return new SuspendDomainResponse($response);
    }

    /**
     * @param string $domain
     * @return UnsuspendDomainResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function unsuspendDomain(string $domain): UnsuspendDomainResponse
    {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
        ];

        $response = $this->post('domain/domain/unsuspend', $payload);

        return new UnsuspendDomainResponse($response);
    }

    /**
     * @param string $sld
     * @param array $tlds
     * @return CheckMultipleTLDAvailabilityResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws APIException
     */
    public function checkMultipleTLDAvailability(string $sld, array $tlds = []): CheckMultipleTLDAvailabilityResponse
    {
        $payload = [
            'sld' => $sld,
            'tlds' => $tlds,
        ];

        $response = $this->post('domain/domain/checkTlds', $payload);

        return new CheckMultipleTLDAvailabilityResponse($response);
    }

    /**
     * @param string $domain
     * @return DomainInfoResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function getDomain(string $domain): DomainInfoResponse
    {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
        ];

        $response = $this->post('domain/domain/info', $payload);

        return new DomainInfoResponse($response);
    }

    /**
     * @param string $domain
     * @param bool $useManagedNameservers
     * @param string[] $customNameservers
     * @return UpdateNameserversResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function updateNameservers(
        string $domain,
        bool $useManagedNameservers,
        array $customNameservers
    ): UpdateNameserversResponse {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
            'dns' => $useManagedNameservers ? 'managed' : 'custom',
        ];

        if (!$useManagedNameservers) {
            $payload = array_merge($payload, $this->normaliseNameservers($customNameservers));
        }

        $response = $this->post('domain/domain/nsUpdate', $payload);

        return new UpdateNameserversResponse($response);
    }

    /**
     * @param string $domain
     * @return DomainEPPKeyResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function getDomainEPPAuthKey(string $domain): DomainEPPKeyResponse
    {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
        ];

        $response = $this->post('domain/domain/eppKey', $payload);

        return new DomainEPPKeyResponse($response);
    }

    /**
     * @param string $domain
     * @return CancelDomainUpdateResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function cancelDomainUpdate(string $domain): CancelDomainUpdateResponse
    {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
        ];

        $response = $this->post('domain/domain/cancelUpdate', $payload);

        return new CancelDomainUpdateResponse($response);
    }

    /**
     * @param string $domain
     * @return CancelDomainDeleteResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function cancelDomainDelete(string $domain): CancelDomainDeleteResponse
    {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
        ];

        $response = $this->post('domain/domain/cancelDelete', $payload);

        return new CancelDomainDeleteResponse($response);
    }

    /**
     * @param string $domain
     * @param bool $autorenew
     * @return SetDomainAutoRenewResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function setDomainAutoRenew(string $domain, bool $autorenew): SetDomainAutoRenewResponse
    {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
            'autorenew' => $autorenew ? 'true' : 'false',
        ];

        $response = $this->post('domain/domain/autorenew', $payload);

        return new SetDomainAutoRenewResponse($response);
    }

    /**
     * @param string $domain
     * @return SetDomainAutoRenewResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function enableDomainAutoRenew(string $domain): SetDomainAutoRenewResponse
    {
        return $this->setDomainAutoRenew($domain, true);
    }

    /**
     * @param string $domain
     * @return SetDomainAutoRenewResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function disableDomainAutoRenew(string $domain): SetDomainAutoRenewResponse
    {
        return $this->setDomainAutoRenew($domain, false);
    }

    /**
     * @param string $domain
     * @return CheckDomainTransferResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function checkDomainTransfer(string $domain): CheckDomainTransferResponse
    {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
        ];

        $response = $this->post('domain/domain/transferCheck', $payload);

        return new CheckDomainTransferResponse($response);
    }

    /**
     * @param string $domain
     * @return CancelDomainTransferResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function cancelDomainTransfer(string $domain): CancelDomainTransferResponse
    {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
        ];

        $response = $this->post('domain/domain/transferCancel', $payload);

        return new CancelDomainTransferResponse($response);
    }

    /**
     * @return DomainTotalsSummaryResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDomainTotalsSummary(): DomainTotalsSummaryResponse
    {
        $response = $this->post('domain/domain/domainTotals');

        return new DomainTotalsSummaryResponse($response);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param string $sort (name|dateRegistered|dateExpiring)
     * @param string $dir (ascending|descending)
     * @param string|null $filter (eg: expired|expiring14|customDNS)
     * @param string|null $search
     * @return DomainListingResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDomains(
        int $limit = 500,
        int $offset = 0,
        string $sort = 'name',
        string $dir = 'ascending',
        ?string $filter = null,
        ?string $search = null
    ): DomainListingResponse {
        $payload = [
            'startPoint' => $offset,
            'limit' => $limit,
            'filter' => $filter,
            'sortBy' => $sort,
            'order' => $dir,
            'search' => $search,
        ];

        $response = $this->post('domain/domain/domainList', $payload);

        return new DomainListingResponse($response);
    }

    /**
     * @param string $query
     * @param int $limit
     * @param int $offset
     * @param string $sort (name|dateRegistered|dateExpiring)
     * @param string $dir (ascending|descending)
     * @param string|null $filter (eg: expired|expiring14|customDNS)
     * @return DomainListingResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchDomains(
        string $query,
        int $limit = 500,
        int $offset = 0,
        string $sort = 'name',
        string $dir = 'ascending',
        ?string $filter = null
    ): DomainListingResponse {
        return $this->getDomains($limit, $offset, $sort, $dir, $filter, $query);
    }

    /**
     * @param string $domain
     * @return DNSRecordsResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function getDNSRecords(string $domain): DNSRecordsResponse
    {
        $domain = Utils::parseDomain($domain);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
        ];

        $response = $this->post('domain/dns/recordList', $payload);

        return new DNSRecordsResponse($response, $domain['fqdn']);
    }

    /**
     * @param string $domain
     * @param ResourceRecord[] $records
     * @return UpdateDNSRecordsResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function updateDNSRecords(string $domain, array $records): UpdateDNSRecordsResponse
    {
        $domain = Utils::parseDomain($domain);

        $records = ResourceRecordParser::fromResourceRecords($records);

        $payload = [
            'sld' => $domain['sld'],
            'tld' => $domain['tld'],
        ];

        // flatten records
        // eg: $records[0]['name'] => 'value' becomes name0 => 'value'
        foreach ($records as $n => $record) {
            foreach (array_keys($record) as $key) {
                $payload[$key . ($n + 1)] = $record[$key];
            }
        }

        $response = $this->post('domain/dns/updateRecords', $payload);

        return new UpdateDNSRecordsResponse($response);
    }

    /**
     * @param Zone $zone
     * @return UpdateDNSRecordsResponse
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateDNSRecordsFromZone(Zone $zone): UpdateDNSRecordsResponse
    {
        $domain = Utils::transformZoneToDomain($zone->getName());

        return $this->updateDNSRecords($domain, $zone->getResourceRecords());
    }

    /**
     * @param string $domain
     * @param string $filename
     * @return UpdateDNSRecordsResponse
     * @throws APIException
     * @throws \Badcow\DNS\Parser\ParseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateDNSRecordsFromZoneFile(string $domain, string $filename): UpdateDNSRecordsResponse
    {
        $name = Utils::transformDomainToZone($domain);

        $contents = file_get_contents($filename);
        $zone = ZoneParser::parse($name, $contents);

        return $this->updateDNSRecordsFromZone($zone);
    }
}
