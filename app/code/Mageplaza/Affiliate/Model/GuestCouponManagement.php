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

namespace Mageplaza\Affiliate\Model;

use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Mageplaza\Affiliate\Api\GuestCouponManagementInterface;
use Mageplaza\Affiliate\Api\CouponManagementInterface;

/**
 * Class GuestCouponManagement
 *
 * @package Mageplaza\Affiliate\Model
 */
class GuestCouponManagement implements GuestCouponManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var CouponManagementInterface
     */
    private $couponManagement;

    /**
     * GuestCouponManagement constructor.
     *
     * @param CouponManagementInterface $couponManagement
     * @param QuoteIdMaskFactory        $quoteIdMaskFactory
     */
    public function __construct(
        CouponManagementInterface $couponManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->couponManagement   = $couponManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $affiliateCouponCode)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->couponManagement->set($quoteIdMask->getQuoteId(), $affiliateCouponCode);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->couponManagement->remove($quoteIdMask->getQuoteId());
    }
}
