<?php

namespace Fovoso\Shipping\Model\ResourceModel\ShippingFree;

use Fovoso\Shipping\Model\ResourceModel\ShippingFree as ResourceModel;
use Fovoso\Shipping\Model\ShippingFree as Model;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Fovoso\Shipping\Model\ResourceModel\ShippingFree
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'fovoso_shipping_collection';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
