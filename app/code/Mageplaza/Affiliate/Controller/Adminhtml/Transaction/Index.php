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
 * Class Index
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Transaction
 */
class Index extends Transaction
{
    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $this->_eventManager->dispatch('mp_affiliate_transaction', ['result_page' => $resultPage]);
        $resultPage->setActiveMenu('Mageplaza_Affiliate::transaction');
        $resultPage->getConfig()->getTitle()->prepend((__('Transactions')));

        return $resultPage;
    }
}
