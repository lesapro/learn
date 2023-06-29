<?php

namespace RLTSquare\ProductReviewImages\Observer;

use Magento\Framework\Event\Observer;

class ProductReviewPageSizeAfter implements \Magento\Framework\Event\ObserverInterface
{

    public function execute(Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $collection->setPageSize(3);
    }
}
