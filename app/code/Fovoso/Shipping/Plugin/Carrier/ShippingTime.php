<?php

namespace Fovoso\Shipping\Plugin\Carrier;

use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;

class ShippingTime
{
    /**
     * @var ShippingMethodExtensionFactory
     */
    protected $extensionFactory;

    /**
     * Description constructor.
     * @param ShippingMethodExtensionFactory $extensionFactory
     */
    public function __construct(
        ShippingMethodExtensionFactory $extensionFactory
    ) {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @param $subject
     * @param $result
     * @param $rateModel
     * @param $quoteCurrencyCode
     * @return mixed
     */
    public function aroundModelToDataObject($subject, callable $proceed, $rateModel, $quoteCurrencyCode)
    {
        $result = $proceed($rateModel, $quoteCurrencyCode);
        $extensionAttribute = $result->getExtensionAttributes() ?
            $result->getExtensionAttributes()
            :
            $this->extensionFactory->create()
        ;
        $extensionAttribute->setMethodDescription($rateModel->getMethodDescription());
        $result->setExtensionAttributes($extensionAttribute);
        return $result;
    }
}
