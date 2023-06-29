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

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Affiliate\Controller\Adminhtml\Account;
use Mageplaza\Affiliate\Model\AccountFactory;
use RuntimeException;

/**
 * Class Save
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Account
 */
class Save extends Account
{
    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param AccountFactory $accountFactory
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        Context $context,
        AccountFactory $accountFactory,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        CustomerFactory $customerFactory
    ) {
        $this->_customerFactory = $customerFactory;

        parent::__construct($context, $accountFactory, $coreRegistry, $resultPageFactory);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPost('account')) {
            $account = $this->_initAccount();
            $account->addData($data);

            $this->_eventManager->dispatch(
                'affiliate_account_prepare_save',
                ['account' => $account, 'action' => $this]
            );

            $customer          = $this->_customerFactory->create()->load($account->getCustomerId());
            $accountCollection = $this->_accountFactory->create()->getCollection()
                ->addFieldToFilter('customer_id', $account->getCustomerId());
            $numOfAccount      = $accountCollection->getSize();
            if (($account->getId() && $numOfAccount > 1) || (!$account->getId() && $numOfAccount > 0)) {
                $this->messageManager->addError(__(
                    'The customer "%1" has registed as an affiliate already.',
                    $customer->getEmail()
                ));
                $this->_getSession()->setData('affiliate_account_data', $data);

                $resultRedirect->setPath('affiliate/*/*', ['_current' => true]);

                return $resultRedirect;
            }

            $account->setData('parent', null);
            if (isset($data['parent']) && is_numeric($data['parent'])) {
                $parent = $this->_accountFactory->create()->load($data['parent']);
                if ($parent && $parent->getId()) {
                    $account->setData('parent', $parent->getId());
                    $parentEmail = $this->_customerFactory->create()->load($parent->getCustomerId())->getEmail();
                    $account->setParentEmail($parentEmail);
                } else {
                    $this->messageManager->addNoticeMessage(__('Cannot find account referred.'));
                }
            }

            try {
                $account->save();
                $this->_getSession()->unsetData('account_customer_id');
                $this->messageManager->addSuccess($account->isObjectNew() ? __('The Account has been created successfully.') : __('The Account has been saved successfully.'));
                $this->_getSession()->setData('affiliate_account_data', false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('affiliate/*/edit', ['id' => $account->getId()]);

                    return $resultRedirect;
                }

                $resultRedirect->setPath('affiliate/*/');

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Account.'));
            }
            $this->_getSession()->setData('affiliate_account_data', $data);
            $resultRedirect->setPath('affiliate/*/*', ['_current' => true]);

            return $resultRedirect;
        }
        $resultRedirect->setPath('affiliate/*/');

        return $resultRedirect;
    }
}
