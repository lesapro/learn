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

namespace Mageplaza\Affiliate\Controller\Account;

use Exception;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Affiliate\Controller\Account;
use Mageplaza\Affiliate\Helper\Data as DataHelper;
use Mageplaza\Affiliate\Model\Account\Status;
use Mageplaza\Affiliate\Model\AccountFactory;
use Mageplaza\Affiliate\Model\TransactionFactory;
use Mageplaza\Affiliate\Model\WithdrawFactory;
use Mageplaza\Affiliate\Model\Email;

/**
 * Class Signuppost
 *
 * @package Mageplaza\Affiliate\Controller\Account
 */
class Signuppost extends Account
{
    /**
     * @var Email
     */
    private $email;

    /**
     * Signuppost constructor.
     *
     * @param Context            $context
     * @param PageFactory        $resultPageFactory
     * @param TransactionFactory $transactionFactory
     * @param AccountFactory     $accountFactory
     * @param WithdrawFactory    $withdrawFactory
     * @param DataHelper         $dataHelper
     * @param CustomerSession    $customerSession
     * @param Registry           $registry
     * @param Email              $email
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TransactionFactory $transactionFactory,
        AccountFactory $accountFactory,
        WithdrawFactory $withdrawFactory,
        DataHelper $dataHelper,
        CustomerSession $customerSession,
        Registry $registry,
        Email $email
    ) {
        $this->email = $email;

        parent::__construct(
            $context,
            $resultPageFactory,
            $transactionFactory,
            $accountFactory,
            $withdrawFactory,
            $dataHelper,
            $customerSession,
            $registry
        );
    }

    /**
     * @return Page|void
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute()
    {
        $account = $this->dataHelper->getCurrentAffiliate();
        if ($account && $account->getId()) {
            if (!$account->isActive()) {
                $this->messageManager->addNoticeMessage(__('Your account is not active. Please contact us.'));
            }
            $this->_redirect('*/*');

            return;
        }

        $postValue = $this->getRequest()->getPostValue();
        if ($this->dataHelper->isEnableTermsAndConditions() && !isset($postValue['terms'])) {
            $this->messageManager->addErrorMessage(__('You have to agree with term and conditions.'));
            $this->_redirect('*/*');

            return;
        }
        $data = [];

        $customer            = $this->customerSession->getCustomer();
        $data['customer_id'] = $customer->getId();
        $signUpConfig        = $this->dataHelper->getAffiliateAccountSignUp();
        $data['group_id']    = $signUpConfig['default_group'];

        if (isset($postValue['referred_by'])) {
            /** @var \Mageplaza\Affiliate\Model\Account $parent */
            $parent = $this->dataHelper->getAffiliateByEmailOrCode(strtolower(trim($postValue['referred_by'])));
            $data['parent']       = $parent->getId();
            $data['parent_email'] = $parent->getCustomer()->getEmail();
        }
        $data['status']             = $signUpConfig['admin_approved'] ? Status::NEED_APPROVED : Status::ACTIVE;
        $data['email_notification'] = $signUpConfig['default_email_notification'];

        try {
            $account->addData($data)->save();
            $messageSuccess = __('Congratulations! You have successfully registered.');
            if ($account->getStatus() == Status::NEED_APPROVED) {
                $messageSuccess = __('Congratulations! You have successfully registered. We will review your affiliate account and inform you once it\'s approved!');
            }

            $this->messageManager->addSuccessMessage($messageSuccess);
            $this->_redirect('*/*');
            if ($this->dataHelper->isEnableAffiliateSignUpEmail()) {
                $this->email->sendEmailToAdmin(['customer' => $customer], DataHelper::XML_PATH_EMAIL_SIGN_UP_TEMPLATE);
            }

            return;
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the Account.'));
        }

        $this->_redirect('*/*/signup');
    }
}
