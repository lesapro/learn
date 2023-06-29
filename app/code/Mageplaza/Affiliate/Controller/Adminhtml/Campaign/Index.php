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

namespace Mageplaza\Affiliate\Controller\Adminhtml\Campaign;

use Magento\Framework\View\Result\Page;
use Mageplaza\Affiliate\Controller\Adminhtml\Campaign;

/**
 * Class Index
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Campaign
 */
class Index extends Campaign
{
    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_Affiliate::campaign');
        $resultPage->getConfig()->getTitle()->prepend((__('Campaigns')));

        return $resultPage;
    }
}
