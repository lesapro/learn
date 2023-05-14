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
use Mageplaza\Affiliate\Helper\Calculation;
use Zend_Serializer_Exception;

/**
 * Class InvoiceSaveAfter
 * @package Mageplaza\Affiliate\Observer
 */
class InvoiceSaveAfter implements ObserverInterface
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
     * @param Observer $observer
     *
     * @return $this
     * @throws Zend_Serializer_Exception
     */
    public function execute(Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        if ($order->getAffiliateEarnCommissionInvoiceAfter()) {
            $this->calculation->calculateCommissionOrder($invoice, 'affiliate_commission_invoiced', 'order/invoice');
        }

        return $this;
    }
}
