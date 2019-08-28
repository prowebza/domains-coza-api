<?php

namespace Balfour\DomainsResellerAPI;

interface RegistrantInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @return string
     */
    public function getContactNumber(): string;

    /**
     * @return string
     */
    public function getAddressLine1(): string;

    /**
     * @return string|null
     */
    public function getAddressLine2(): ?string;

    /**
     * @return string
     */
    public function getPostalCode(): string;

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string;

    /**
     * @return string|null
     */
    public function getCompany(): ?string;

    /**
     * @return string
     */
    public function getCity(): string;

    /**
     * @return string
     */
    public function getProvince(): string;
}
