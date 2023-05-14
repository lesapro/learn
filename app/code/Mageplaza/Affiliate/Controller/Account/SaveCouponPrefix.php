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

namespace Mageplaza\Affiliate\Controller\Account;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\Page;
use Mageplaza\Affiliate\Controller\Account;

/**
 * Class SaveCouponPrefix
 * @package Mageplaza\Affiliate\Controller\Account
 */
class SaveCouponPrefix extends Account
{
    /**
     * @return Redirect|Page
     */
    public function execute()
    {
        $couponPrefix       = $this->getRequest()->getParam('campaign_coupon_prefix');
        $affiliateAccount   = $this->accountFactory->create()->loadByCode($couponPrefix);
        $affiliateAccountId = $affiliateAccount->getId();

        $currentAffiliate   = $this->accountFactory->create()->load(
            $this->customerSession->getCustomerId(),
            'customer_id'
        );
        $currentAffiliateId = $currentAffiliate->getId();

        if ($currentAffiliateId) {
            if ($affiliateAccountId === null || $affiliateAccountId === $currentAffiliateId) {
                try {
                    $currentAffiliate->setData('code', $couponPrefix)->save();
                    $this->messageManager->addSuccessMessage(__('Successfully'));
                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            } else {
                $this->messageManager->addErrorMessage(__('Coupon prefix is exists.'));
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*');
    }
}
