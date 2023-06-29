<?php

namespace Isobar\OneFieldName\Observer\Config\Section;

use Isobar\OneFieldName\Api\CustomerManagementInterface;
use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class OneFieldName
 * @package Isobar\OneFieldName\Observer\Config\Section
 */
class OneFieldName implements ObserverInterface
{
    /**
     * @var string
     */
    const CUSTOMER_EAV_ATTRIBUTE_WEBSITE_TABLE = 'customer_eav_attribute_website';

    /**
     * @var array
     */
    private $websiteIdsToMerge = [];

    /**
     * @var array
     */
    private $websiteIdsToRevert = [];

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var CustomerManagementInterface
     */
    protected $customerManagement;

    /**
     * OneFieldName constructor.
     *
     * @param DataHelper $dataHelper
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resourceConnection
     * @param CustomerManagementInterface $customerManagement
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        DataHelper $dataHelper,
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection,
        CustomerManagementInterface $customerManagement,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
        $this->customerManagement = $customerManagement;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @inheritDoc
     *
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $websiteIds = [];
        $websiteId = $observer->getEvent()->getWebsite();
        if (!empty($websiteId)) {
            $websiteIds[] = (int)$websiteId;
        } else {
            $websites = $this->storeManager->getWebsites();
            foreach ($websites as $website) {
                $websiteIds[] = $website->getId();
            }
        }

        foreach ($websiteIds as $websiteId) {
            $isShow = $this->dataHelper->isShowOneFieldName($websiteId);
            $this->updateAttributePerWebsite($websiteId, !$isShow);

            if ($isShow === true) {
                $this->websiteIdsToMerge[] = $websiteId;
            } else {
                $this->websiteIdsToRevert[] = $websiteId;
            }
        }
        if (!empty($this->websiteIdsToMerge)) {
            $this->customerManagement->mergeCustomerName($this->websiteIdsToMerge);
        }

        if (!empty($this->websiteIdsToRevert)) {
            $this->customerManagement->revertCustomerName($this->websiteIdsToRevert);
        }
    }

    /**
     * @param int $websiteId
     * @param bool $value
     *
     * @return void
     */
    private function updateAttributePerWebsite($websiteId, $value)
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName(self::CUSTOMER_EAV_ATTRIBUTE_WEBSITE_TABLE);
        $attributeIds = $this->getAttributeIds();
        if (!empty($attributeIds)) {
            foreach ($attributeIds as $attributeId) {
                $connection->insertOnDuplicate(
                    $table,
                    [
                        'attribute_id' => $attributeId,
                        'website_id' => $websiteId,
                        'is_visible' => $value,
                        'is_required' => $value
                    ],
                    ['attribute_id', 'website_id', 'is_visible', 'is_required']
                );
            }
        }
    }

    /**
     * @param string $entityTypeCode
     * @param string $attributeCode
     *
     * @return int|null
     */
    private function getAttributeId($entityTypeCode, $attributeCode)
    {
        try {
            $attribute = $this->attributeRepository->get($entityTypeCode, $attributeCode);
            return $attribute->getAttributeId();
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @return array
     */
    private function getAttributeIds()
    {
        $attributeIds = [];
        $customerLastNameId = $this->getAttributeId(Customer::ENTITY, CustomerInterface::LASTNAME);
        $addressLastNameId = $this->getAttributeId('customer_address', AddressInterface::LASTNAME);
        if (!empty($customerLastNameId)) {
            $attributeIds[] = $customerLastNameId;
        }
        if (!empty($addressLastNameId)) {
            $attributeIds[] = $addressLastNameId;
        }
        return $attributeIds;
    }
}
