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

namespace Mageplaza\Affiliate\Controller\Adminhtml\Withdraw;

use Magento\Framework\View\Result\Page;
use Mageplaza\Affiliate\Controller\Adminhtml\Withdraw;

/**
 * Class Edit
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Withdraw
 */
class Edit extends Withdraw
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|Page
     */
    public function execute()
    {
        /** @var \Mageplaza\Affiliate\Model\Withdraw $withdraw */
        $withdraw = $this->_initWithdraw();

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_Affiliate::withdraw');
        $resultPage->getConfig()->getTitle()->set(__('Withdraws'));

        $title = $withdraw->getId() ? __('Edit Withdraw "#%1"', $withdraw->getId()) : __('New Withdraw');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $this->_coreRegistry->register('current_withdraw', $withdraw);

        return $resultPage;
    }
}
