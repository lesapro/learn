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

namespace Mageplaza\Affiliate\Controller\Adminhtml\Group;

use Magento\Framework\View\Result\Page;
use Mageplaza\Affiliate\Controller\Adminhtml\Group;

/**
 * Class Index
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Group
 */
class Index extends Group
{
    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_Affiliate::group');
        $resultPage->getConfig()->getTitle()->prepend((__('Group')));

        return $resultPage;
    }
}
