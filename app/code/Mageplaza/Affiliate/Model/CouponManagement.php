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

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Mageplaza\Affiliate\Api\CouponManagementInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\Affiliate\Helper\Data;

/**
 * Class CouponManagement
 * @package Mageplaza\Affiliate\Model
 */
class CouponManagement implements CouponManagementInterface
{
    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var CampaignFactory
     */
    private $campaignFactory;

    /**
     * CouponManagement constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param Data $helperData
     * @param CampaignFactory $campaignFactory
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Data $helperData,
        CampaignFactory $campaignFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->helperData      = $helperData;
        $this->campaignFactory = $campaignFactory;
    }

    /**
     * @inheritDoc
     */
    public function set($cartId, $affiliateCouponCode)
    {
        /** @var  Quote $quote */
        $quote   = $this->quoteRepository->getActive($cartId);
        $storeId = $quote->getStoreId();
        if (!$this->helperData->isEnabled($storeId) || !$this->helperData->isUseCodeAsCoupon($storeId)) {
            throw new CouldNotSaveException(__('The extension is disabled.'));
        }

        $couponWithPreFix = explode('-', $affiliateCouponCode);
        if (count($couponWithPreFix) !== 2) {
            throw new NoSuchEntityException(__("The coupon code isn't valid. Verify the code and try again."));
        }

        /** @var Campaign $campaign */
        $campaign = $this->campaignFactory->create()->load($couponWithPreFix[1], 'coupon_code');

        $currentAffiliate = $this->helperData->getAffiliateAccount($quote->getCustomerId(), 'customer_id');
        $affiliateAccount = $this->helperData->getAffiliateAccount($couponWithPreFix[0], 'code');

        if (!$affiliateAccount->getId() || ($currentAffiliate && $currentAffiliate->getId()) || !$campaign->getId()) {
            throw new NoSuchEntityException(__("The coupon code isn't valid. Verify the code and try again."));
        }

        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }
        if (!$quote->getStoreId()) {
            throw new NoSuchEntityException(__('Cart isn\'t assigned to correct store'));
        }

        $this->helperData->setAffiliateKeyToCookie($affiliateCouponCode);
        $this->helperData->setAffiliateKeyToCookie('coupon', Data::AFFILIATE_COOKIE_SOURCE_NAME);
        $quote->collectTotals()->save();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function remove($cartId)
    {
        /** @var  Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $this->helperData->deleteAffiliateCookieSourceName();
        $this->helperData->resetAffiliate($quote);
        $quote->collectTotals()->save();

        return true;
    }
}
