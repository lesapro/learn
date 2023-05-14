<?php

namespace Fovoso\Shipping\Model;

use Fovoso\Shipping\Model\ResourceModel\ShippingFree as ResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * Class ShippingFree
 * @package Fovoso\Shipping\Model
 */
class ShippingFree extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'fovoso_shipping_model';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
