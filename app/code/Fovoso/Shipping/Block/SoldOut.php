<?php

namespace Fovoso\Shipping\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class ShippingPolicy
 * @package Fovoso\Shipping\Block
 */
class SoldOut extends \Magento\Framework\View\Element\Template
{
    private \Magento\Framework\Registry $_registry;
    private \Magento\Catalog\Api\ProductRepositoryInterface $productRepository;

    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productRepository = $productRepository;
        $this->_registry = $registry;
    }

    /**
     * @return string
     */
    public function getSoldOut()
    {
        $product = $this->_registry->registry('current_product');
        return $product->getSoldOut();
    }
}
