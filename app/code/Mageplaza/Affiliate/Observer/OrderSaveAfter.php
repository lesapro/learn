<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Affiliate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Affiliate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Mageplaza\Affiliate\Helper\Calculation;
use Mageplaza\Affiliate\Model\TransactionFactory;
use Zend_Serializer_Exception;

/**
 * Class OrderSaveAfter
 *
 * @package Mageplaza\Affiliate\Observer
 */
class OrderSaveAfter implements ObserverInterface
{
    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var Calculation
     */
    protected $calculation;

    /**
     * OrderSaveAfter constructor.
     *
     * @param TransactionFactory $transactionFactory
     * @param Calculation        $calculation
     */
    public function __construct(
        TransactionFactory $transactionFactory,
        Calculation $calculation
    ) {
        $this->_transactionFactory = $transactionFactory;
        $this->calculation         = $calculation;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws Zend_Serializer_Exception
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order->getAffiliateEarnCommissionInvoiceAfter()
            && $order->getState() == Order::STATE_COMPLETE
            && $order->getAffiliateCommission()) {
            $transaction = $this->_transactionFactory->create()->getCollection()
                ->addFieldToFilter('action', 'order/invoice')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFirstItem();
            if ($transaction->getId()) {
                return $this;
            }

            $totalTierCommission = [];
            if ($order->hasCreditmemos()) {
                foreach ($order->getItems() as $item) {
                    $qty            = $item->getQtyOrdered() - $item->getQtyRefunded();
                    $itemCommission = $this->calculation->unserialize($item->getAffiliateCommission());
                    if ($itemCommission && $qty > 0) {
                        $totalItemCommission = $this->calculation->getTotalTierCommission($itemCommission);
                        $this->calculation->setTotalTierCommission(
                            $totalTierCommission,
                            $totalItemCommission,
                            $qty / $item->getQtyOrdered()
                        );
                    }
                }
                $shippingCommission = $this->calculation->parseCommissionOnTier(
                    $order->getAffiliateShippingCommission()
                );
                $this->calculation->setTotalTierCommission($totalTierCommission, $shippingCommission);
            } else {
                $commission          = $this->calculation->unserialize($order->getAffiliateCommission());
                $totalTierCommission = $this->calculation->getTotalTierCommission($commission);
            }

            $this->calculation->createTransactionByAction($order, $totalTierCommission, 'order/invoice');
        }

        return $this;
    }
}
