<?php

namespace Isobar\OneFieldName\Plugin\Magento\Checkout\Block\Checkout;

use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Checkout\Block\Checkout\AttributeMerger as BaseAttributeMerger;
use Magento\Customer\Api\Data\AddressInterface;

/**
 * Class AttributeMerger
 * @package Isobar\OneFieldName\Plugin\Magento\Checkout\Block\Checkout
 */
class AttributeMerger
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * AttributeMerger constructor.
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param BaseAttributeMerger $subject
     * @param array $elements
     * @param string $providerName
     * @param string $dataScopePrefix
     * @param array $fields
     *
     * @return array
     */
    public function beforeMerge(
        BaseAttributeMerger $subject,
        $elements,
        $providerName,
        $dataScopePrefix,
        array $fields = []
    ) {
        if ($this->dataHelper->isShowOneFieldName()
            && isset($elements[AddressInterface::LASTNAME])
        ) {
            unset($elements[AddressInterface::LASTNAME]);
        }
        return [$elements, $providerName, $dataScopePrefix, $fields];
    }
}
