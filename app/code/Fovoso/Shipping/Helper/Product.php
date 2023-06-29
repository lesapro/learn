<?php

namespace Fovoso\Shipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Product extends AbstractHelper
{
    private \Magento\Framework\Registry $_registry;

    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(Context $context, \Magento\Framework\Registry $registry)
    {
        parent::__construct($context);
        $this->_registry = $registry;
    }

    /**
     * @return string
     */
    public function getSoldOut()
    {
        $product = $this->_registry->registry('current_product');
        return $product ? $product->getSoldOut() : 0;
    }
}
