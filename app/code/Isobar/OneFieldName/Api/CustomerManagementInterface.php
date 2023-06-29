<?php

namespace Isobar\OneFieldName\Api;

/**
 * Interface CustomerManagementInterface
 * @package Isobar\OneFieldName\Api
 */
interface CustomerManagementInterface
{
    /**
     * Merge customer's first name and last name
     *
     * @param array $websiteId
     *
     * @throws \Exception
     */
    public function mergeCustomerName(array $websiteId);

    /**
     * Revert customer's first name and last name
     *
     * @param array $websiteIds
     *
     * @throws \Exception
     */
    public function revertCustomerName(array $websiteIds);
}
