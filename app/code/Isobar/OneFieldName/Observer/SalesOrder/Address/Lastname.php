<?php

namespace Isobar\OneFieldName\Observer\SalesOrder\Address;

use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

/**
 * Class Lastname
 * @package Isobar\OneFieldName\Observer\SalesOrder\Address
 */
class Lastname implements ObserverInterface
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * Lastname constructor.
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        $websiteId = $this->dataHelper->getWebsiteIdByStoreId($order->getStoreId());
        if ($this->dataHelper->isShowOneFieldName($websiteId)) {
            if (!empty($order->getShippingAddress()->getLastname())) {
                $order->getShippingAddress()->setLastname(null);
            }
            if (!empty($order->getBillingAddress()->getLastname())) {
                $order->getBillingAddress()->setLastname(null);
            }
        }

        return $this;
    }
}
