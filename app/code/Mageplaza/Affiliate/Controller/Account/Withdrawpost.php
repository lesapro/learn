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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Affiliate\Controller\Account;
use Mageplaza\Affiliate\Helper\Data as DataHelper;
use Mageplaza\Affiliate\Model\AccountFactory;
use Mageplaza\Affiliate\Model\TransactionFactory;
use Mageplaza\Affiliate\Model\WithdrawFactory;
use RuntimeException;
use Mageplaza\Affiliate\Model\Email;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Withdrawpost
 *
 * @package Mageplaza\Affiliate\Controller\Account
 */
class Withdrawpost extends Account
{
    /**
     * @var Email
     */
    private $email;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * Withdrawpost constructor.
     *
     * @param Context                $context
     * @param PageFactory            $resultPageFactory
     * @param TransactionFactory     $transactionFactory
     * @param AccountFactory         $accountFactory
     * @param WithdrawFactory        $withdrawFactory
     * @param DataHelper             $dataHelper
     * @param CustomerSession        $customerSession
     * @param Registry               $registry
     * @param Email                  $email
     * @param StoreManagerInterface  $storeManager
     * @param PriceCurrencyInterface $priceCurrency
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
        Email $email,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->email         = $email;
        $this->storeManager  = $storeManager;
        $this->priceCurrency = $priceCurrency;

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
     * @inheritdoc
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $account = $this->dataHelper->getCurrentAffiliate();
        if (!$account || !$account->getId() || !$account->isActive()) {
            $this->messageManager->addNoticeMessage(__('An error occur. Please contact us.'));

            return $this->_redirect('*/*');
        }
        $customer  = $this->customerSession->getCustomer();
        $postValue = $this->getRequest()->getPostValue();

        $amount                 = $this->convertToBaseCurrency($postValue['amount']);
        $data                   = [];
        $data['customer_id']    = $customer->getId();
        $data['account_id']     = $account->getId();
        $data['amount']         = $amount;
        $data['payment_method'] = $postValue['payment_method'];
        if (isset($postValue['offline_address'])) {
            $data['offline_address'] = $postValue['offline_address'];
        }
        if (isset($postValue['banktranfer'])) {
            $data['banktranfer'] = $postValue['banktranfer'];
        }
        if (isset($postValue['paypal_email'])) {
            $data['paypal_email'] = $postValue['paypal_email'];
        }
        $data['withdraw_description'] = $postValue['withdraw_description'];

        $this->customerSession->setWithdrawFormData($data);
        $withdraw = $this->withdrawFactory->create();
        $withdraw->addData($data)->setAccount($account);

        try {
            $this->checkWithdrawAmount($withdraw);
            $withdraw->save();
            $this->messageManager->addSuccessMessage(__('Your request has been sent successfully. We will review your request and inform you once it\'s approved!'));
            $this->customerSession->setWithdrawFormData(false);
            if ($this->dataHelper->isEnableWithdrawRequestEmail()) {
                $this->email->sendEmailToAdmin(
                    compact('customer', 'data'),
                    DataHelper::XML_PATH_EMAIL_WITHDRAW_REQUEST_TEMPLATE
                );
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the request.'));
        }

        return $this->_redirect('*/*/withdraw');
    }

    /**
     * @param $withdraw
     *
     * @return $this
     * @throws LocalizedException
     */
    public function checkWithdrawAmount($withdraw)
    {
        $minBalance = $this->dataHelper->getWithdrawMinimumBalance();
        if ($minBalance && $withdraw->getAccount()->getBalance() < $minBalance) {
            throw new LocalizedException(__('Your balance is not enough for request withdraw.'));
        }

        $min = $this->dataHelper->getWithdrawMinimum();
        if ($min && $withdraw->getAmount() < $min) {
            throw new LocalizedException(__(
                'The withdraw amount have to equal or greater than %1',
                $this->dataHelper->formatPrice($min)
            ));
        }

        $max = $this->dataHelper->getWithdrawMaximum();
        if ($max && $withdraw->getAmount() > $max) {
            throw new LocalizedException(__(
                'The withdraw amount have to equal or less than %1',
                $this->dataHelper->formatPrice($max)
            ));
        }

        return $this;
    }

    /**
     * @param float $currentPrice
     *
     * @return float
     * @throws NoSuchEntityException
     */
    public function convertToBaseCurrency($currentPrice)
    {
        $store = $this->storeManager->getStore();
        $rate  = $this->priceCurrency->convert($currentPrice, $store) / $currentPrice;

        return $currentPrice / $rate;
    }
}
