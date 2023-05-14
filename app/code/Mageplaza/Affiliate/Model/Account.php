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

namespace Mageplaza\Affiliate\Model;

use Exception;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\Affiliate\Helper\Data as DataHelper;
use Mageplaza\Affiliate\Model\Account\Status;

/**
 * Class Account
 *
 * @package Mageplaza\Affiliate\Model
 */
class Account extends AbstractModel
{
    const XML_PATH_EMAIL_ENABLE = 'email/account/enable';
    const XML_PATH_ACCOUNT_EMAIL_WELCOME_TEMPLATE = 'affiliate/email/account/welcome';
    const XML_PATH_ACCOUNT_EMAIL_APPROVE_TEMPLATE = 'affiliate/email/account/approve';
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'affiliate_account';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'affiliate_account';

    /**
     * @type DataHelper
     */
    protected $_helper;

    /**
     * @type CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Object Manager
     *
     * @type
     */
    protected $objectManager;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Account constructor.
     *
     * @param Context                $context
     * @param Registry               $registry
     * @param DataHelper             $helper
     * @param CustomerFactory        $customerFactory
     * @param ObjectManagerInterface $objectmanager
     * @param Email                  $email
     * @param StoreManagerInterface  $storeManager
     * @param AbstractResource|null  $resource
     * @param AbstractDb|null        $resourceCollection
     * @param array                  $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataHelper $helper,
        CustomerFactory $customerFactory,
        ObjectManagerInterface $objectmanager,
        Email $email,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_helper          = $helper;
        $this->_customerFactory = $customerFactory;
        $this->objectManager    = $objectmanager;
        $this->email            = $email;
        $this->storeManager     = $storeManager;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\Affiliate\Model\ResourceModel\Account');
    }

    /**
     * @return Account|void
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function afterSave()
    {
        parent::afterSave();
        $storeId = $this->storeManager->getStore()->getId();
        if ($this->isObjectNew()) {
            $this->sendWelcomeEmail();
        }

        if ($this->hasDataChanges()) {
            $origStatus = (int)$this->getOrigData('status');
            $status     = (int)$this->getData('status');
            if ($origStatus === Status::NEED_APPROVED && $status === Status::ACTIVE) {
                $this->sendApproveEmail();
            }
            if ($origStatus === Status::NEED_APPROVED
                && $status === Status::INACTIVE
                && $this->_helper->isEnableAccountRejectionEmail($storeId)) {
                $customer = $this->_customerFactory->create()->load($this->getCustomerId());
                $account  = $this;

                $this->email->sendEmailTemplate(
                    $customer->getEmail(),
                    $customer->getName(),
                    DataHelper::XML_PATH_EMAIL_ACCOUNT_REJECTION_TEMPLATE,
                    compact('customer', 'account'),
                    $storeId
                );
            }

            if ((($origStatus === Status::INACTIVE && $status === Status::ACTIVE)
                    || ($origStatus === Status::ACTIVE && $status === Status::INACTIVE))
                && $this->_helper->isEnableAccountChangeStatusEmail($storeId)
                && $this->getData('email_notification')
            ) {
                $customer      = $this->_customerFactory->create()->load($this->getCustomerId());
                $account       = $this;
                $accountStatus = $status === Status::ACTIVE ? __('Active') : __('Inactive');

                $this->email->sendEmailTemplate(
                    $customer->getEmail(),
                    $customer->getName(),
                    DataHelper::XML_PATH_EMAIL_ACCOUNT_CHANGE_STATUS_TEMPLATE,
                    compact('account', 'customer', 'accountStatus'),
                    $storeId
                );
            }
        }
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param $code
     *
     * @return $this
     */
    public function loadByCode($code)
    {
        return $this->load($code, 'code');
    }

    /**
     * @param $customer
     *
     * @return $this
     */
    public function loadByCustomer($customer)
    {
        return $this->loadByCustomerId($customer->getId());
    }

    /**
     * @param $customerId
     *
     * @return $this
     */
    public function loadByCustomerId($customerId)
    {
        return $this->load($customerId, 'customer_id');
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->_customerFactory->create()->load($this->getCustomerId());
    }

    /**
     * @return mixed
     */
    public function getPricingHelper()
    {
        return $this->objectManager->create(Data::class);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getBalanceFormated($store)
    {
        return $this->getPricingHelper()->currencyByStore($this->getBalance(), $store->getId(), true, false);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getStatus() == Status::ACTIVE;
    }

    /**
     * @throws Exception
     */
    public function sendWelcomeEmail()
    {
        $this->_sendEmail(self::XML_PATH_ACCOUNT_EMAIL_WELCOME_TEMPLATE);
    }

    /**
     * @throws Exception
     */
    public function sendApproveEmail()
    {
        $this->_sendEmail(self::XML_PATH_ACCOUNT_EMAIL_APPROVE_TEMPLATE);
    }

    /**
     * @param $template
     * @return $this
     * @throws Exception
     */
    protected function _sendEmail($template)
    {
        if (!$this->_helper->allowSendEmail($this, self::XML_PATH_EMAIL_ENABLE)) {
            return $this;
        }

        $customer = $this->getCustomer();
        if (!$customer || !$customer->getId()) {
            return $this;
        }

        try {
            $this->_helper->sendEmailTemplate($customer, $template, ['account' => $this]);
        } catch (LocalizedException $e) {
            $this->_logger->debug($e->getMessage().".\x20Please check the email server and retry.");
            return $this;
        }

        return $this;
    }
}
