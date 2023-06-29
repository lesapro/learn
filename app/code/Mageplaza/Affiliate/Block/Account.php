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

namespace Mageplaza\Affiliate\Block;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Affiliate\Helper\Data as AffiliateHelper;
use Mageplaza\Affiliate\Helper\Payment;
use Mageplaza\Affiliate\Model\AccountFactory;
use Mageplaza\Affiliate\Model\CampaignFactory;
use Mageplaza\Affiliate\Model\TransactionFactory;
use Mageplaza\Affiliate\Model\WithdrawFactory;
use Mageplaza\Affiliate\Model\WithdrawhistoryFactory;

/**
 * Class Account
 * @package Mageplaza\Affiliate\Block
 */
abstract class Account extends Template
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var View
     */
    protected $_helperView;

    /**
     * @var CurrentCustomer
     */
    protected $customerSession;

    /**
     * @var AffiliateHelper
     */
    protected $_affiliateHelper;

    /**
     * @var PriceHelper
     */
    protected $pricingHelper;

    /**
     * @var CampaignFactory
     */
    protected $campaignFactory;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var null
     */
    protected $_currentAccount = null;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var WithdrawFactory
     */
    protected $withdrawFactory;

    /**
     * @var Payment
     */
    protected $paymentHelper;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Account constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param View $helperView
     * @param AffiliateHelper $affiliateHelper
     * @param Payment $paymentHelper
     * @param JsonHelper $jsonHelper
     * @param Registry $registry
     * @param PriceHelper $pricingHelper
     * @param ObjectManagerInterface $objectManager
     * @param CampaignFactory $campaignFactory
     * @param AccountFactory $accountFactory
     * @param WithdrawFactory $withdrawFactory
     * @param TransactionFactory $transactionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        View $helperView,
        AffiliateHelper $affiliateHelper,
        Payment $paymentHelper,
        JsonHelper $jsonHelper,
        Registry $registry,
        PriceHelper $pricingHelper,
        ObjectManagerInterface $objectManager,
        CampaignFactory $campaignFactory,
        AccountFactory $accountFactory,
        WithdrawFactory $withdrawFactory,
        TransactionFactory $transactionFactory,
        array $data = []
    ) {
        $this->pricingHelper      = $pricingHelper;
        $this->objectManager      = $objectManager;
        $this->customerSession    = $customerSession;
        $this->_helperView        = $helperView;
        $this->_affiliateHelper   = $affiliateHelper;
        $this->jsonHelper         = $jsonHelper;
        $this->paymentHelper      = $paymentHelper;
        $this->registry           = $registry;
        $this->accountFactory     = $accountFactory;
        $this->campaignFactory    = $campaignFactory;
        $this->transactionFactory = $transactionFactory;
        $this->withdrawFactory    = $withdrawFactory;

        parent::__construct($context, $data);
    }

    /**
     * Returns the Magento Customer Model for this block
     *
     * @return CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            return $this->customerSession->getCustomer();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @return \Mageplaza\Affiliate\Model\Account
     */
    public function getCurrentAccount()
    {
        if ($this->_currentAccount === null) {
            $this->_currentAccount = $this->_affiliateHelper->getCurrentAffiliate();
        }

        return $this->_currentAccount;
    }

    /**
     * @return AffiliateHelper
     */
    public function getAffiliateHelper()
    {
        return $this->_affiliateHelper;
    }

    /**
     * @param number $blockIdentify
     * @param bool|string $title
     *
     * @return string
     */
    public function loadCmsBlock($blockIdentify, $title = false)
    {
        return $this->getAffiliateHelper()->loadCmsBlock($blockIdentify, $title);
    }

    /**
     * @param $price
     *
     * @return float|string
     */
    public function formatPrice($price)
    {
        return $this->pricingHelper->currency($price);
    }
}
