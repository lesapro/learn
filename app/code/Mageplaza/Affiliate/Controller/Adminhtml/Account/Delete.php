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

namespace Mageplaza\Affiliate\Controller\Adminhtml\Account;

use Exception;
use Mageplaza\Affiliate\Controller\Adminhtml\Account;

/**
 * Class Delete
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Account
 */
class Delete extends Account
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /** @var \Mageplaza\Affiliate\Model\Account $account */
                $account = $this->_accountFactory->create();
                $account->load($id);
                $customer_id = $account->getCustomerId();
                $account->delete();
                $this->messageManager->addSuccess(__('The Account has been deleted.'));
                $this->_eventManager->dispatch('affiliate_account_delete_success', ['customer_id' => $customer_id]);

                $resultRedirect->setPath('affiliate/*/');

                return $resultRedirect;
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('affiliate/*/edit', ['id' => $id]);

                return $resultRedirect;
            }
        }
        $this->messageManager->addError(__('Account to delete was not found.'));
        $resultRedirect->setPath('affiliate/*/');

        return $resultRedirect;
    }
}
