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

namespace Mageplaza\Affiliate\Helper\Calculation;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\Affiliate\Helper\Calculation;
use Zend_Serializer_Exception;

/**
 * Class Discount
 * @package Mageplaza\Affiliate\Helper\Calculation
 */
class Discount extends Calculation
{
    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     *
     * @return $this|Calculation
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $items = $shippingAssignment->getItems();

        if (!$items || !$this->canCalculate($quote->getStoreId(), true) || $quote->getIsMultiShipping()) {
            $this->resetAffiliate($quote);

            return $this;
        }

        $itemFields = ['affiliate_discount_amount', 'base_affiliate_discount_amount'];
        $this->resetAffiliateData(
            $items,
            $quote,
            array_merge($itemFields, ['affiliate_discount_shipping_amount', 'affiliate_base_discount_shipping_amount']),
            $itemFields
        );
        $account            = $this->registry->registry('mp_affiliate_account');
        $isBreakCampaign    = false;
        $affiliateKey       = $this->getAffiliateKey(); // if no cookie then first order key
        $affSource          = $this->getAffiliateSourceFromCookie(self::AFFILIATE_COOKIE_SOURCE_NAME);
        $campaignCouponCode = null;

        if ($affSource === 'coupon') {
            $affCodeWithPrefix = explode('-', $affiliateKey);
            if (count($affCodeWithPrefix) === 2) {
                $campaignCouponCode = $affCodeWithPrefix[1];
            } else {
                return $this;
            }
        }

        foreach ($this->getAvailableCampaign($account, $campaignCouponCode) as $campaign) {
            $isCalculateTax      = $campaign->getApplyDiscountOnTax();
            $isCalculateShipping = $campaign->getApplyToShipping();
            $totalCart           = $this->getTotalOnCart($items, $quote, $isCalculateShipping, $isCalculateTax, false);

            if ($totalCart <= 0.001) {
                break;
            }

            $totalDiscount = $this->getDiscountOnCampaign($campaign, $totalCart);
            if ($quote->getBaseAffiliateDiscountAmount() + $totalDiscount > $totalCart) {
                $totalDiscount   = $totalCart - $quote->getBaseAffiliateDiscountAmount();
                $isBreakCampaign = true;
            }
            $baseDiscount = $discount = 0;

            if ($totalDiscount) {
                $lastItem = null;
                foreach ($items as $item) {
                    if ($item->getParentItem()) {
                        continue;
                    }

                    if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                        /** @var Item $child */
                        foreach ($item->getChildren() as $child) {
                            $this->calculateItemDiscount(
                                $child,
                                $totalCart,
                                $totalDiscount,
                                $baseDiscount,
                                $discount,
                                $isCalculateTax,
                                $lastItem
                            );
                        }
                    } else {
                        $this->calculateItemDiscount(
                            $item,
                            $totalCart,
                            $totalDiscount,
                            $baseDiscount,
                            $discount,
                            $isCalculateTax,
                            $lastItem
                        );
                    }
                }

                if ($campaign->getApplyToShipping()) {
                    $shippingBaseDiscount = $totalDiscount - $baseDiscount;
                    $shippingDiscount     = $this->priceCurrency->convert($shippingBaseDiscount);
                    $quote->setAffiliateDiscountShippingAmount(
                        $quote->getAffiliateDiscountShippingAmount() + $shippingDiscount
                    );
                    $quote->setBaseAffiliateDiscountShippingAmount(
                        $shippingBaseDiscount
                    );
                    $baseDiscount += $shippingBaseDiscount;
                    $discount     += $shippingDiscount;
                } elseif ($lastItem && $totalDiscount > $baseDiscount) {
                    $lastItemBaseDiscount = $totalDiscount - $baseDiscount;
                    $lastItemDiscount     = $this->priceCurrency->convert($lastItemBaseDiscount);
                    $lastItem->setBaseAffiliateDiscountAmount(
                        $item->getBaseAffiliateDiscountAmount() + $lastItemBaseDiscount
                    )->setAffiliateDiscountAmount($item->getAffiliateDiscountAmount() + $lastItemDiscount);
                    $baseDiscount += $lastItemBaseDiscount;
                    $discount     += $lastItemDiscount;
                }

                $quote->setAffiliateDiscountAmount($quote->getAffiliateDiscountAmount() + $discount);
                $quote->setBaseAffiliateDiscountAmount($quote->getBaseAffiliateDiscountAmount() + $baseDiscount);
                $baseGrandTotal = $total->getBaseGrandTotal() - $baseDiscount;
                $grandTotal     = $total->getGrandTotal() - $discount;
                if ($grandTotal <= 0.0001) {
                    $baseGrandTotal = $grandTotal = 0;
                }

                $quote->setAffiliateKey($this->getAffiliateKeyFromCookie());
                $total->setBaseGrandTotal($baseGrandTotal);
                $total->setGrandTotal($grandTotal);
                $quote->setBaseGrandTotal($baseGrandTotal);
                $quote->setGrandTotal($grandTotal);
                $quote->save();

                if ($isBreakCampaign) {
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * @param $item
     * @param $total
     * @param $totalDiscount
     * @param $baseDiscount
     * @param $discount
     * @param $isCalculateTax
     * @param $lastItem
     */
    public function calculateItemDiscount(
        $item,
        $total,
        $totalDiscount,
        &$baseDiscount,
        &$discount,
        $isCalculateTax,
        &$lastItem
    ) {
        $itemBaseDiscount = ($this->getItemTotalForDiscount($item, $isCalculateTax, false) / $total) * $totalDiscount;
        $itemBaseDiscount = $this->round($itemBaseDiscount, 'base');
        $itemDiscount     = $this->convertPrice($itemBaseDiscount, false, false, $item->getStoreId());
        $itemDiscount     = $this->round($itemDiscount);
        $item->setBaseAffiliateDiscountAmount($item->getBaseAffiliateDiscountAmount() + $itemBaseDiscount)
            ->setAffiliateDiscountAmount($item->getAffiliateDiscountAmount() + $itemDiscount);
        $baseDiscount += $itemBaseDiscount;
        $discount     += $itemDiscount;
        $lastItem     = $item;
    }
}
