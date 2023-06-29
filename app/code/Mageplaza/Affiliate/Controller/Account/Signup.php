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

use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\Page;
use Mageplaza\Affiliate\Controller\Account;

/**
 * Class Signup
 * @package Mageplaza\Affiliate\Controller\Account
 */
class Signup extends Account
{
    /**
     * @return Redirect|Page
     */
    public function execute()
    {
        $account = $this->dataHelper->getCurrentAffiliate();
        if ($account && $account->getId()) {
            if (!$account->isActive()) {
                $this->messageManager->addNoticeMessage(__('Your account is not active. Please contact us.'));
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*');

            return $resultRedirect;
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setHeader('Login-Required', 'true');

        return $resultPage;
    }
}
