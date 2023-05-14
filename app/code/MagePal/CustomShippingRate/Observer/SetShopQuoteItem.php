<?php

namespace MagePal\CustomShippingRate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SetShopQuoteItem
 * @package MagePal\CustomShippingRate\Observer
 */
class SetShopQuoteItem implements ObserverInterface
{
    private \Magento\Catalog\Api\ProductRepositoryInterface $productRepository;

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $item  = $observer->getEvent()->getItem();
        $sku = $item['product']['sku'];
        $product = $this->productRepository->get($sku);
        if (!empty($product->getShop())) {
            $item->setData('shop', $product->getShop());
        }
    }
}
