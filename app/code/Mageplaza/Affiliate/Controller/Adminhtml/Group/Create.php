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
 * Class Create
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Group
 */
class Create extends Group
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|Page
     */
    public function execute()
    {
        /** @var \Mageplaza\Affiliate\Model\Group $group */
        $group = $this->_initGroup();
        $data = $this->_getSession()->getData('affiliate_group_data', true);
        if (!empty($data)) {
            $group->setData($data);
        }
        $this->_coreRegistry->register('current_group', $group);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_Affiliate::group');
        $resultPage->getConfig()->getTitle()->set(__('Groups'));

        $resultPage->getConfig()->getTitle()->prepend(__('New Group'));

        return $resultPage;
    }
}
