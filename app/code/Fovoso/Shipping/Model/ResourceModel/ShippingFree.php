<?php

namespace Fovoso\Shipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ShippingFree
 * @package Fovoso\Shipping\Model\ResourceModel
 */
class ShippingFree extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'fovoso_shipping_resource_model';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('fovoso_shipping', 'id');
        $this->_useIsObjectNew = true;
    }
}
