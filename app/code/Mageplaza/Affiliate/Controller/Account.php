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

namespace Mageplaza\Affiliate\Controller;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Affiliate\Helper\Data as DataHelper;
use Mageplaza\Affiliate\Model\AccountFactory;
use Mageplaza\Affiliate\Model\TransactionFactory;
use Mageplaza\Affiliate\Model\WithdrawFactory;

/**
 * Class Account
 * @package Mageplaza\Affiliate\Controller
 */
abstract class Account extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var WithdrawFactory
     */
    protected $withdrawFactory;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Account constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TransactionFactory $transactionFactory
     * @param AccountFactory $accountFactory
     * @param WithdrawFactory $withdrawFactory
     * @param DataHelper $dataHelper
     * @param CustomerSession $customerSession
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TransactionFactory $transactionFactory,
        AccountFactory $accountFactory,
        WithdrawFactory $withdrawFactory,
        DataHelper $dataHelper,
        CustomerSession $customerSession,
        Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->transactionFactory = $transactionFactory;
        $this->accountFactory = $accountFactory;
        $this->withdrawFactory = $withdrawFactory;
        $this->dataHelper = $dataHelper;
        $this->customerSession = $customerSession;
        $this->registry = $registry;

        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
