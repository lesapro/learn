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
use Mageplaza\Affiliate\Helper\Data as DataHelper;

/**
 * Class SalesConvertQuote
 * @package Mageplaza\Affiliate\Observer
 */
class SalesConvertQuote implements ObserverInterface
{
    /**
     * @var DataHelper
     */
    protected $_helper;

    /**
     * SalesConvertQuote constructor.
     *
     * @param DataHelper $helper
     */
    public function __construct(DataHelper $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if (!$this->_helper->isEnabled() || !$quote->getAffiliateKey()) {
            return $this;
        }

        $order = $observer->getEvent()->getOrder();
        $order->setAffiliateKey($quote->getAffiliateKey())
            ->setAffiliateEarnCommissionInvoiceAfter($this->_helper->getEarnCommissionInvoiceAfter())
            ->setAffiliateCommission($quote->getAffiliateCommission())
            ->setAffiliateShippingCommission($quote->getAffiliateShippingCommission())
            ->setAffiliateDiscountAmount($quote->getAffiliateDiscountAmount())
            ->setBaseAffiliateDiscountAmount($quote->getBaseAffiliateDiscountAmount())
            ->setBaseAffiliateDiscountShippingAmount($quote->getBaseAffiliateDiscountShippingAmount())
            ->setAffiliateDiscountShippingAmount($quote->getAffiliateDiscountShippingAmount());

        foreach ($order->getItems() as $item) {
            $quoteItem = $quote->getItemById($item->getQuoteItemId());
            if (!$quoteItem) {
                continue;
            }

            $item->setAffiliateDiscountAmount($quoteItem->getAffiliateDiscountAmount())
                ->setBaseAffiliateDiscountAmount($quoteItem->getBaseAffiliateDiscountAmount())
                ->setAffiliateCommission($quoteItem->getAffiliateCommission());
        }

        return $this;
    }
}
