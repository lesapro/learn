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

namespace Mageplaza\Affiliate\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Affiliate\Model\GroupFactory;

/**
 * Class Group
 * @package Mageplaza\Affiliate\Controller\Adminhtml
 */
abstract class Group extends AbstractAction
{
    /**
     * Group Factory
     *
     * @var GroupFactory
     */
    protected $_groupFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Group constructor.
     *
     * @param Context $context
     * @param GroupFactory $groupFactory
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        GroupFactory $groupFactory,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->_groupFactory = $groupFactory;
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context, $resultPageFactory, $coreRegistry);
    }

    /**
     * @return mixed
     */
    protected function _initGroup()
    {
        $groupId = (int)$this->getRequest()->getParam('id');
        /** @var \Mageplaza\Affiliate\Model\Group $group */
        $group = $this->_groupFactory->create();
        if ($groupId) {
            $group->load($groupId);
            if (!$group->getId()) {
                $this->messageManager->addError(__('This account no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('affiliate/account/index');

                return $resultRedirect;
            }
        }

        return $group;
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageplaza_Affiliate::group');
    }
}
