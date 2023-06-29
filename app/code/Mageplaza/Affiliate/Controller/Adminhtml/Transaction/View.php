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

use Magento\Framework\View\Result\Page;
use Mageplaza\Affiliate\Controller\Adminhtml\Transaction;

/**
 * Class View
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Transaction
 */
class View extends Transaction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|Page
     */
    public function execute()
    {
        $transactionId = (int)$this->getRequest()->getParam('id');
        /** @var \Mageplaza\Affiliate\Model\Transaction $transaction */
        $transaction = $this->_transactionFactory->create();
        if ($transactionId) {
            $transaction->load($transactionId);
        }
        $this->_coreRegistry->register('current_transaction', $transaction);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_Affiliate::transaction');
        $resultPage->getConfig()->getTitle()->set(__('Transactions'));

        $title = __('View Transaction "%1"', $transaction->getId());
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
