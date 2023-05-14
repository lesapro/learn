<?php

namespace Isobar\OneFieldName\Plugin\Magento\Customer\Model\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer\DataProviderWithDefaultAddresses as BaseDataProviderWithDefaultAddresses;

/**
 * Class DataProviderWithDefaultAddresses
 * @package Isobar\OneFieldName\Plugin\Magento\Customer\Model\Customer
 */
class DataProviderWithDefaultAddresses
{
    /**
     * @param BaseDataProviderWithDefaultAddresses $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetMeta(BaseDataProviderWithDefaultAddresses $subject, array $result)
    {
        if (isset($result['customer'])
            && isset($result['customer']['children'])
            && isset($result['customer']['children'][CustomerInterface::LASTNAME])
            && !empty($result['customer']['children'][CustomerInterface::LASTNAME])
            && $result['customer']['children'][CustomerInterface::LASTNAME]['arguments']['data']['config']['visible'] === false
        ) {
            $result['customer']['children'][CustomerInterface::LASTNAME]['arguments']['data']['config']['visible'] = true;
        }
        return $result;
    }
}
