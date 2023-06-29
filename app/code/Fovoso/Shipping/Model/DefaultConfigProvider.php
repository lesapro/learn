<?php

namespace Fovoso\Shipping\Model;

use Magento\Shipping\Model\Config as ShippingConfig;
use Magento\Shipping\Model\Carrier\AbstractCarrier;

/**
 * Class DefaultConfigProvider
 * @package Fovoso\Shipping\Model
 */
class DefaultConfigProvider
{
    /**
     * @var ShippingConfig
     */
    protected $shippingMethodConfig;

    /**
     * Constructor.
     *
     * @param ShippingConfig $shippingConfig
     */
    public function __construct(
        ShippingConfig $shippingConfig
    ) {
        $this->shippingMethodConfig = $shippingConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return [
            'shipping' => [
                'carriers' => $this->_getActiveCarriers()
            ]
        ];
    }

    /**
     * Returns active carriers codes
     *
     * @return array
     */
    private function _getActiveCarriers()
    {
        $activeCarriers = [];
        /** @var AbstractCarrier $carrier */
        foreach ($this->shippingMethodConfig->getActiveCarriers() as $carrier) {
            $activeCarriers[$carrier->getCarrierCode()] = [
                'code' => $carrier->getCarrierCode(),
                'description' => 'tetststst kkkkk' ?: ''
            ];
        }

        return $activeCarriers;
    }
}
