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

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Affiliate\Controller\Adminhtml\Account;
use Mageplaza\Affiliate\Model\AccountFactory;

/**
 * Class Edit
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Account
 */
class Edit extends Account
{
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param AccountFactory $accountFactory
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        AccountFactory $accountFactory,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;

        parent::__construct($context, $accountFactory, $coreRegistry, $resultPageFactory);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect|Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_Affiliate::account');
        $resultPage->getConfig()->getTitle()->set(__('Accounts'));

        /** @var \Mageplaza\Affiliate\Model\Account $account */
        $account = $this->_initAccount();

        $data = $this->_getSession()->getData('affiliate_account_data', true);
        if (!empty($data)) {
            $account->setData($data);
        }
        $this->_coreRegistry->register('current_account', $account);

        $title = $account->getId() ? __('Edit Account "%1"', $account->getId()) : __('New Account');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
