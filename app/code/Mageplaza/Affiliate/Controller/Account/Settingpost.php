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
use Magento\Framework\View\Result\Page;
use Mageplaza\Affiliate\Controller\Account;

/**
 * Class Settingpost
 * @package Mageplaza\Affiliate\Controller\Account
 */
class Settingpost extends Account
{
    /**
     * @return Page|void
     */
    public function execute()
    {
        $accountData = $this->getRequest()->getParam('account');
        $account = $this->dataHelper->getCurrentAffiliate();
        if (!$account || !$account->getId()) {
            $this->_redirect('*/*/');
        }

        try {
            $emailNotification = isset($accountData['email_notification']) ? $accountData['email_notification'] : 0;
            $account->setEmailNotification($emailNotification)->save();
            $this->messageManager->addSuccessMessage(__('Saved successfully!'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the account.'));
        }

        $this->_redirect('*/account/setting');
    }
}
