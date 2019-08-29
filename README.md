# domains-coza-api

An integration with the domains.co.za reseller API.

*This library is in early release and is pending unit tests.*

## Table of Contents

* [Installation](#installation)
* [Usage](#usage)
    * [Creating a Client](#creating-a-client)
    * [Register Domain](#register-domain)
    * [Check Domain Availability](#check-domain-availability)
    * [Delete Domain](#delete-domain)
    * [Update Domain Registrant](#update-domain-registrant)
    * [Renew Domain](#renew-domain)
    * [Transfer Domain](#transfer-domain)
    * [Suspend Domain](#suspend-domain)
    * [Unsuspend Domain](#unsuspend-domain)
    * [Check Multiple TLD Availability](#check-multiple-tld-availability)
    * [Retrieve Domain Info](#retrieve-domain-info)
    * [Update Nameservers](#update-nameservers)
    * [Retrieve Domain EPP Auth Key](#retrieve-domain-epp-auth-key)
    * [Cancel Domain Update](#cancel-domain-update)
    * [Cancel Domain Delete](#cancel-domain-delete)
    * [Set Domain AutoRenew](#set-domain-autorenew)
    * [Check Domain Transfer](#check-domain-transfer)
    * [Cancel Domain Transfer](#cancel-domain-transfer)
    * [Retrieve Domain Totals Summary](#retrieve-domain-totals-summary)
    * [List Domains](#list-domains)
    * [Search Domains](#search-domains)
    * [Retrieve DNS Records](#retrieve-dns-records)
    * [Update DNS Records](#update-dns-records)

## Installation

```bash
composer require balfour/domains-coza-api
```
    
## Usage

Please see https://www.domains.co.za/api/latest_api.pdf for full API documentation.

### Creating a Client

```php
use GuzzleHttp\Client as Guzzle;
use Balfour\DomainsResellerAPI\Client;

$guzzle = new Guzzle();
$client = new Client($guzzle, 'your-api-key');
```

### Register Domain

```php
$response = $client->registerDomain(
    'mydomain.co.za',
    'Balfour Group (Pty) Ltd',
    'dns-admin@moo.com',
    '+27.211111111',
    'My Address Line 1',
    'My Optional Address Line 2',
    '8001',
    'ZA',
    'Balfour Group (Pty) Ltd',
    'Cape Town',
    'Western Cape',
    1, // years - max of 1 year for co.za domains
    true, // use managed nameservers
    [],
    'TEST1' // optional external ref
);

// using custom nameservers
$response = $client->registerDomain(
    'mydomain.co.za',
    'Balfour Group (Pty) Ltd',
    'dns-admin@moo.com',
    '+27.211111111',
    'My Address Line 1',
    'My Optional Address Line 2',
    '8001',
    'ZA',
    'Balfour Group (Pty) Ltd',
    'Cape Town',
    'Western Cape',
    1, // years - max of 1 year for co.za domains
    false, // not using managed nameservers
    [
        'ns1.foo.bar',
        'ns2.foo.bar',
        'ns3.foo.bar',
        'ns4.foo.bar',
        'ns5.foo.bar',
    ],
    'TEST1' // optional external ref
);

// you can also register a domain using an implementation of RegistrantInterface
// eg: assuming $registrant is an implementation
$response = $client->registerDomainForRegistrant('mydomain.co.za', $registrant);
```

### Check Domain Availability

```php
$isAvailable = $client->isDomainAvailable('mydomain.co.za');
```

### Delete Domain

```php
$response = $client->deleteDomain('mydomain.co.za');
```

### Update Domain Registrant

```php
$response = $client->updateDomainRegistrant(
    'mydomain.co.za',
    'Balfour Group (Pty) Ltd',
    'dns-admin@moo.com',
    '+27.211111111',
    'My Address Line 1',
    'My Optional Address Line 2',
    '8001',
    'ZA',
    'Balfour Group (Pty) Ltd',
    'Cape Town',
    'Western Cape'
);

// you can also use an implementation of RegistrantInterface
$client->updateDomainRegistrantFromRegistrant('mydomain.co.za', $registrant);
```

### Renew Domain

```php
$response = $client->renewDomain('mydomain.co.za', 1);
```

### Transfer Domain

```php
$response = $client->transferDomain(
    'mydomain.co.za',
    'Balfour Group (Pty) Ltd',
    'dns-admin@moo.com',
    '+27.211111111',
    'My Address Line 1',
    'My Optional Address Line 2',
    '8001',
    'ZA',
    'Balfour Group (Pty) Ltd',
    'Cape Town',
    'Western Cape',
    null, // epp key (if required for tld)
    'keep', // possible values are keep, managed or custom
    [], // cusotm nameservers - only used if dns type is set to 'custom'
    'TEST1' // optional external ref
);

// you can also use an implementation of RegistrantInterface
$client->transferDomainForRegistrant(
    'mydomain.co.za',
    $registrant,
    null, // epp key (if required for tld)
    'keep', // possible values are keep, managed or custom
    [], // cusotm nameservers - only used if dns type is set to 'custom'
    'TEST1' // optional external ref
);
```

### Suspend Domain

```php
$response = $client->suspendDomain('mydomain.co.za');
```

### Unsuspend Domain

```php
$response = $client->unsuspendDomain('mydomain.co.za');
```

### Check Multiple TLD Availability

```php
$response = $client->checkMultipleTLDAvailability('mydomain');
var_dump($response->getTLDs());
var_dump($response->getAvailableTLDs());
var_dump($response->getAvailableTLDs());
var_dump($response->getTakenTLDs());
var_dump($response->getTLD('co.za'));
var_dump($response->isTLDAvailable('co.za'));
```

### Retrieve Domain Info

```php
$response = $client->getDomain('mydomain.co.za');

$contacts = $response->getContacts();
var_dump($contacts);

$registrant = $response->getRegistrant();
var_dump($registrant->getContactNumber());
var_dump($registrant->hasPendingUpdate());
var_dump($registrant->getPendingUpdate()->getExpectedChangeDate());

var_dump($response->getNameservers());

var_dump($response->getCreationDate());
```

### Update Nameservers

```php
// use managed dns
$response = $client->updateNameservers('mydomain.co.za', true);

// use custom nameservers
$response = $client->updateNameservers(
    'mydomain.co.za',
    false,
    [
        'ns1.foo.bar',
        'ns2.foo.bar',
        'ns3.foo.bar',
        'ns4.foo.bar',
        'ns5.foo.bar',
    ]
);
```

### Retrieve Domain EPP Auth Key

```php
$response = $client->getDomainEPPAuthKey('mydomain.co.za');
var_dump($response->getEPPKey());
```

### Cancel Domain Update

```php
$response = $client->cancelDomainUpdate('mydomain.co.za');
```

### Cancel Domain Delete

```php
$response = $client->cancelDomainDelete('mydomain.co.za');
```

### Set Domain AutoRenew

```php
$response = $client->setDomainAutoRenew('mydomain.co.za', true);
$response = $client->setDomainAutoRenew('mydomain.co.za', false);
$response = $client->enableDomainAutoRenew('mydomain.co.za');
$response = $client->disableDomainAutoRenew('mydomain.co.za');
```

### Check Domain Transfer

```php
$response = $client->checkDomainTransfer('mydomain.co.za');
var_dump($response->getRequestDate());
var_dump($response->getStatus());
var_dump($response->isComplete());
```

### Cancel Domain Transfer

```php
$response = $client->cancelDomainTransfer('mydomain.co.za');
```

### Retrieve Domain Totals Summary

```php
$response = $client->getDomainTotalsSummary();
var_dump($response->getSummary());
var_dump($response->getTotalTransfersIn());
```

### List Domains

```php
$response = $client->getDomains();

// using limit and offset
$response = $client->getDomains(15, 0);

// sorting results
$response = $client->getDomains(15, 0, 'dateRegistered');
$response = $client->getDomains(15, 0, 'dateRegistered', 'descending');

// filtering results
$response = $client->getDomains(15, 0, 'name', 'ascending', 'expiring90');

var_dump($response->getTotal());

foreach ($response->getDomains() as $domain) {
    var_dump($domain->getName());
    var_dump($domain->isPremiumDNSEnabled());
    var_dump($domain->getCreationDate());
    var_dump($domain->getNameservers());
}
```

### Search Domains

```php
$response = $client->searchDomains('mydomain');

foreach ($response->getDomains() as $domain) {
    var_dump($domain->getName());
    var_dump($domain->getStatus());
}
```

### Retrieve DNS Records

```php
use Badcow\DNS\AlignedBuilder;

$response = $client->getDNSRecords('mydomain.co.za');

var_dump($response->toArray());

foreach ($response->getRecords() as $record) {
    var_dump($record->getType());
    var_dump($record->getName());
    var_dump($record->getContent());
    var_dump($record->getPriority()); // only applicable to MX records
    var_dump($record->getTTL());
}

// filter by type of record
$records = $response->getRecordsByType('MX');

// the records can be formatted as a zone file
$zone = $response->getZone();
echo AlignedBuilder::build($zone);
```

### Update DNS Records

**Please Note:**

1. The API only supports A, AAAA, CNAME, MX and TXT records.
1. The zone file is replaced in full upon each update.

```php
use Badcow\DNS\Rdata\Factory;
use Badcow\DNS\ResourceRecord;

// this example assumes no existing records
$a = new ResourceRecord;
$a->setName('sub.domain');
$a->setTtl(3600);
$a->setRdata(Factory::A('127.0.0.1'));

$mx = new ResourceRecord();
$mx->setName('@');
$mx->setRdata(Factory::Mx(10, 'mail-gw1.example.net.'));

$response = $client->updateDNSRecords('mydomain.co.za', [$a, $mx]);

// here, we first fetch the existing records, add a new record to the zone, then update passing in the zone
$response = $client->getDNSRecords('mydomain.co.za');
$zone = $response->getZone();

$a = new ResourceRecord;
$a->setName('sub.domain');
$a->setTtl(3600);
$a->setRdata(Factory::A('127.0.0.1'));

$zone->addResourceRecord($a);

// notice how we're just passing in a 
$client->updateDNSRecordsFromZone($zone);

// you can also update the records from a local zone file
$client->updateDNSRecordsFromZoneFile('mydomain.co.za', '/path/to/zonefile');
```
