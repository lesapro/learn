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

namespace Mageplaza\Affiliate\Controller\Account\Withdraw;

use Mageplaza\Affiliate\Controller\Account;

/**
 * Class View
 * @package Mageplaza\Affiliate\Controller\Account\Withdraw
 */
class View extends Account
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $customerId = $this->customerSession->getId();
        $id         = $this->getRequest()->getParam('id');
        $withdraw   = $this->withdrawFactory->create()->load($id);

        if (!$withdraw || !$withdraw->getId() || $withdraw->getCustomerId() !== $customerId) {
            $this->messageManager->addErrorMessage(__('Cannot find item.'));

            return $this->_redirect('*/account/withdraw');
        }

        $this->registry->register('withdraw_view_data', $withdraw);

        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
