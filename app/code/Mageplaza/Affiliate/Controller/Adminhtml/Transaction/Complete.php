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

namespace Mageplaza\Affiliate\Controller\Adminhtml\Transaction;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Mageplaza\Affiliate\Controller\Adminhtml\Transaction;

/**
 * Class Complete
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Transaction
 */
class Complete extends Transaction
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $transactionId = $this->getRequest()->getParam('id');
        if ($transactionId) {
            $transaction = $this->_transactionFactory->create()->load($transactionId);
            if ($transaction->getId()) {
                try {
                    $transaction->complete();
                    $this->messageManager->addSuccess(__('The Transaction has been completed.'));
                } catch (Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
        }
        $resultRedirect->setPath('affiliate/*/');

        return $resultRedirect;
    }
}
