<?php

namespace Isobar\OneFieldName\Helper;

use Isobar\OneFieldName\Model\Config\Source\MergeType;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\Model\Session\Quote as QuoteSession;
use Magento\Customer\Model\Customer;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Address;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 * @package Isobar\OneFieldName\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Default website id
     */
    const DEFAULT_WEBSITE_ID = 0;

    /**#@+
     * Config path
     *
     * @var string
     */
    const IS_SHOW_ONE_FIELD_NAME = 'one_field_name/configuration/is_show';
    const MERGE_TWO_FIELD_NAME = 'one_field_name/configuration/merge_type';
    /**#@-*/

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var QuoteSession
     */
    protected $quoteSession;

    /**
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param QuoteSession $quoteSession
     * @param BackendSession $backendSession
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        QuoteSession $quoteSession,
        BackendSession $backendSession,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->coreRegistry = $registry;
        $this->storeManager = $storeManager;
        $this->quoteSession = $quoteSession;
        $this->backendSession = $backendSession;
        $this->attributeRepository = $attributeRepository;
        parent::__construct($context);
    }

    /**
     * @param int|null $websiteId
     *
     * @return bool
     */
    public function isShowOneFieldName($websiteId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::IS_SHOW_ONE_FIELD_NAME,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get data by key
     *
     * @param string $key
     *
     * @return int|string|null
     */
    public function getByKey($key)
    {
        $customerAccountData = $this->getCustomerAccountData();
        return ($customerAccountData && isset($customerAccountData[$key]))
            ? $customerAccountData[$key]
            : null;
    }

    /**
     * Get current customer account data in backend session
     *
     * @return array
     */
    private function getCustomerAccountData()
    {
        $customerData = $this->backendSession->getData('customer_data');
        return ($customerData && isset($customerData['account'])) ? $customerData['account'] : [];
    }

    /**
     * Get customer's store label
     *
     * @param string $attributeCode
     * @param int $storeId
     *
     * @return string|null
     */
    public function getCustomerStoreLabel($attributeCode, $storeId)
    {
        try {
            /** @var AttributeInterface $firstNameAttribute */
            $firstNameAttribute = $this->attributeRepository->get(Customer::ENTITY, $attributeCode);
            $storeLabel = $firstNameAttribute->getStoreLabel($storeId);
            return $storeLabel ?: $firstNameAttribute->getDefaultFrontendLabel();
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * Get website id by store id
     *
     * @param int $storeId
     *
     * @return int
     */
    public function getWebsiteIdByStoreId($storeId)
    {
        try {
            return (int)$this->storeManager->getStore($storeId)->getWebsiteId();
        } catch (\Exception $exception) {
            return self::DEFAULT_WEBSITE_ID;
        }
    }

    /**
     * Merge two field firstname and lastname
     *
     * @param string $firstname
     * @param string $lastname
     * @param int|null $websiteId
     *
     * @return string
     */
    public function mergeTwoFieldName($firstname, $lastname, $websiteId = null)
    {
        $merge = (int)$this->scopeConfig->getValue(
            self::MERGE_TWO_FIELD_NAME,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        if ($merge === MergeType::MERGE_LAST_FIRST) {
            return $lastname . ' ' . $firstname;
        }
        return $firstname . ' ' . $lastname;
    }

    /**
     * Get website id in quote session
     *
     * @return int
     *
     * @throws NoSuchEntityException
     */
    public function getWebsiteIdInQuoteSession()
    {
        $storeId = $this->getBackendQuoteSession()->getStoreId();
        if (empty($storeId)) {
            if ($orderAddress = $this->getAddress()) {
                $storeId = $orderAddress->getOrder()->getStoreId();
            } else {
                return self::DEFAULT_WEBSITE_ID;
            }
        }
        return (int)$this->storeManager->getStore($storeId)->getWebsiteId();
    }

    /**
     * Retrieve Backend Quote Session
     *
     * @return QuoteSession
     */
    private function getBackendQuoteSession()
    {
        if (!$this->quoteSession) {
            $this->quoteSession = ObjectManager::getInstance()->get(QuoteSession::class);
        }
        return $this->quoteSession;
    }

    /**
     * Order address getter
     *
     * @return Address
     */
    private function getAddress()
    {
        return $this->coreRegistry->registry('order_address');
    }
}
