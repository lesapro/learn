<?php

namespace Isobar\OneFieldName\Override\Magento\CustomerImportExport\Model\Import;

use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address\Validator\Postcode as PostcodeValidator;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Indexer\Processor as IndexerProcessor;
use Magento\Customer\Model\ResourceModel\Address\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Customer\Model\ResourceModel\Address\Attribute\Source\CountryWithWebsites as CountryWithWebsitesSource;
use Magento\CustomerImportExport\Model\Import\Address as BaseImportAddress;
use Magento\CustomerImportExport\Model\ResourceModel\Import\Address\Storage as AddressStorage;
use Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Export\Factory as CollectionFactory;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper as ResourceHelper;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Address
 * @package Isobar\OneFieldName\Override\Magento\CustomerImportExport\Model\Import
 */
class Address extends BaseImportAddress
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * @var AddressStorage
     */
    private $addressStorage;

    /**
     * @var CountryWithWebsitesSource
     */
    private $countryWithWebsites;

    /**
     * @var IndexerProcessor
     */
    private $indexerProcessor;

    /**
     * Options for certain attributes sorted by websites.
     *
     * @var array[][] With path as <attributeCode> => <websiteID> => options[].
     */
    private $optionsByWebsite = [];

    /**
     * Address constructor.
     *
     * @param DataHelper $dataHelper
     * @param StringUtils $string
     * @param ScopeConfigInterface $scopeConfig
     * @param ImportFactory $importFactory
     * @param ResourceHelper $resourceHelper
     * @param ResourceConnection $resource
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     * @param EavConfig $eavConfig
     * @param StorageFactory $storageFactory
     * @param AddressFactory $addressFactory
     * @param RegionCollectionFactory $regionColFactory
     * @param CustomerFactory $customerFactory
     * @param AttributeCollectionFactory $attributesFactory
     * @param DateTime $dateTime
     * @param PostcodeValidator $postcodeValidator
     * @param array $data
     * @param CountryWithWebsitesSource|null $countryWithWebsites
     * @param AddressStorage|null $addressStorage
     * @param IndexerProcessor|null $indexerProcessor
     */
    public function __construct(
        DataHelper $dataHelper,
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory,
        ResourceHelper $resourceHelper,
        ResourceConnection $resource,
        ProcessingErrorAggregatorInterface $errorAggregator,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        EavConfig $eavConfig,
        StorageFactory $storageFactory,
        AddressFactory $addressFactory,
        RegionCollectionFactory $regionColFactory,
        CustomerFactory $customerFactory,
        AttributeCollectionFactory $attributesFactory,
        DateTime $dateTime,
        PostcodeValidator $postcodeValidator,
        array $data = [],
        ?CountryWithWebsitesSource $countryWithWebsites = null,
        ?AddressStorage $addressStorage = null,
        ?IndexerProcessor $indexerProcessor = null
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct(
            $string,
            $scopeConfig,
            $importFactory,
            $resourceHelper,
            $resource,
            $errorAggregator,
            $storeManager,
            $collectionFactory,
            $eavConfig,
            $storageFactory,
            $addressFactory,
            $regionColFactory,
            $customerFactory,
            $attributesFactory,
            $dateTime,
            $postcodeValidator,
            $data,
            $countryWithWebsites,
            $addressStorage,
            $indexerProcessor
        );
        $this->addressStorage = $addressStorage
            ?: ObjectManager::getInstance()->get(AddressStorage::class);
        $this->indexerProcessor = $indexerProcessor
            ?: ObjectManager::getInstance()->get(IndexerProcessor::class);
    }

    /**
     * @inheritDoc
     */
    protected function _validateRowForUpdate(array $rowData, $rowNumber)
    {
        $multiSeparator = $this->getMultipleValueSeparator();
        if ($this->_checkUniqueKey($rowData, $rowNumber)) {
            $email = strtolower($rowData[self::COLUMN_EMAIL]);
            $website = $rowData[self::COLUMN_WEBSITE];
            $addressId = (int)$rowData[self::COLUMN_ADDRESS_ID];
            $customerId = $this->_getCustomerId($email, $website);

            if ($customerId === false) {
                $this->addRowError(self::ERROR_CUSTOMER_NOT_FOUND, $rowNumber);
            } elseif ($this->_checkRowDuplicate($customerId, $addressId)) {
                $this->addRowError(self::ERROR_DUPLICATE_PK, $rowNumber);
            } else {
                // check simple attributes
                foreach ($this->_attributes as $attributeCode => $attributeParams) {
                    $websiteId = $this->_websiteCodeToId[$website];
                    $attributeParams = $this->adjustAttributeDataForWebsite($attributeParams, $websiteId);

                    /**#@+
                     * Override
                     */
                    if ($attributeCode === AddressInterface::LASTNAME && $this->dataHelper->isShowOneFieldName($websiteId)) {
                        continue;
                    }
                    /**#@-*/

                    if (in_array($attributeCode, $this->_ignoredAttributes)) {
                        continue;
                    } elseif (isset($rowData[$attributeCode]) && strlen($rowData[$attributeCode])) {
                        $this->isAttributeValid(
                            $attributeCode,
                            $attributeParams,
                            $rowData,
                            $rowNumber,
                            $multiSeparator
                        );
                    } elseif ($attributeParams['is_required']
                        && !$this->addressStorage->doesExist(
                            (string)$addressId,
                            (string)$customerId
                        )
                    ) {
                        $this->addRowError(self::ERROR_VALUE_IS_REQUIRED, $rowNumber, $attributeCode);
                    }
                }

                if (!empty($rowData[self::COLUMN_COUNTRY_ID])) {
                    if (isset($rowData[self::COLUMN_POSTCODE])
                        && !$this->postcodeValidator->isValid(
                            $rowData[self::COLUMN_COUNTRY_ID],
                            $rowData[self::COLUMN_POSTCODE]
                        )
                    ) {
                        $this->addRowError(self::ERROR_VALUE_IS_REQUIRED, $rowNumber, self::COLUMN_POSTCODE);
                    }

                    if (!empty($rowData[self::COLUMN_REGION])
                        && count($this->getCountryRegions($rowData[self::COLUMN_COUNTRY_ID])) > 0
                        && null === $this->getCountryRegionId(
                            $rowData[self::COLUMN_COUNTRY_ID],
                            $rowData[self::COLUMN_REGION]
                        )
                    ) {
                        $this->addRowError(self::ERROR_INVALID_REGION, $rowNumber, self::COLUMN_REGION);
                    }
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getAttributeOptions(AbstractAttribute $attribute, array $indexAttributes = [])
    {
        $standardOptions = parent::getAttributeOptions($attribute, $indexAttributes);

        if ($attribute->getAttributeCode() === 'country_id') {
            //If we want to get available options for country field then we have to use alternative source
            // to get actual data for each website.
            $options = $this->getCountryWithWebsitesSource()->getAllOptions();
            //Available country options now will be sorted by websites.
            $code = $attribute->getAttributeCode();
            $websiteOptions = [Store::DEFAULT_STORE_ID => $standardOptions];
            //Sorting options by website.
            foreach ($options as $option) {
                if (array_key_exists('website_ids', $option)) {
                    foreach ($option['website_ids'] as $websiteId) {
                        if (!array_key_exists($websiteId, $websiteOptions)) {
                            $websiteOptions[$websiteId] = [];
                        }
                        $optionId = mb_strtolower($option['value']);
                        $websiteOptions[$websiteId][$optionId] = $option['value'];
                    }
                }
            }

            /**#@+
             * Override for private variable
             *
             * Storing sorted
             */
            $this->optionsByWebsite[$code] = $websiteOptions;
            /**#@-*/
        }

        return $standardOptions;
    }

    /**
     * Attributes' data may vary depending on website settings,
     * this method adjusts an attribute's data from $this->_attributes to
     * website-specific data.
     *
     * @param array $attributeData Data from $this->_attributes.
     * @param int $websiteId
     *
     * @return array Adjusted data in the same format.
     */
    private function adjustAttributeDataForWebsite(array $attributeData, int $websiteId): array
    {
        if ($attributeData['code'] === 'country_id') {
            $attributeOptions = $this->optionsByWebsite[$attributeData['code']];
            if (array_key_exists($websiteId, $attributeOptions)) {
                $attributeData['options'] = $attributeOptions[$websiteId];
            }
        }

        return $attributeData;
    }

    /**
     * Get RegionID from the initialized data
     *
     * @param string $countryId
     * @param string $region
     *
     * @return int|null
     */
    private function getCountryRegionId(string $countryId, string $region): ?int
    {
        $countryRegions = $this->getCountryRegions($countryId);
        return $countryRegions[strtolower($region)] ?? null;
    }

    /**
     * Get country regions
     *
     * @param string $countryId
     *
     * @return array
     */
    private function getCountryRegions(string $countryId): array
    {
        return $this->_countryRegions[strtolower($countryId)] ?? [];
    }

    /**
     * Retrieve country with websites source
     *
     * @return CountryWithWebsitesSource
     */
    private function getCountryWithWebsitesSource()
    {
        if (!$this->countryWithWebsites) {
            $this->countryWithWebsites = ObjectManager::getInstance()->get(CountryWithWebsitesSource::class);
        }
        return $this->countryWithWebsites;
    }
}
