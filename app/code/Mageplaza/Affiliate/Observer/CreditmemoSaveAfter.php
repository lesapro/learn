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

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Mageplaza\Affiliate\Helper\Calculation;
use Zend_Serializer_Exception;

/**
 * Class CreditmemoSaveAfter
 * @package Mageplaza\Affiliate\Observer
 */
class CreditmemoSaveAfter implements ObserverInterface
{
    /**
     * @var Calculation
     */
    protected $calculation;

    /**
     * CreditmemoSaveAfter constructor.
     *
     * @param Calculation $calculation
     */
    public function __construct(
        Calculation $calculation
    ) {
        $this->calculation = $calculation;
    }

    /**
     * @param $order
     *
     * @return bool
     */
    public function canRefundCommission($order)
    {
        $isEarnCommissionInvoice = $order->getAffiliateEarnCommissionInvoiceAfter();

        return $this->calculation->isProcessRefund($order->getStoreId())
            && ($isEarnCommissionInvoice
                || (!$isEarnCommissionInvoice
                    && ($order->getState() == Order::STATE_COMPLETE || $order->getState() == Order::STATE_CLOSED)));
    }

    /**
     * @param EventObserver $observer
     *
     * @return $this
     * @throws Zend_Serializer_Exception
     */
    public function execute(EventObserver $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();
        if (!$this->canRefundCommission($order)) {
            return $this;
        }
        $this->calculation->calculateCommissionOrder($creditmemo, 'affiliate_commission_refunded', 'order/refund');

        return $this;
    }
}
