<?php

namespace Isobar\OneFieldName\Override\Magento\CustomerImportExport\Model\Import;

use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Indexer\Processor;
use Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\CustomerImportExport\Model\Import\Customer as BaseImportCustomer;
use Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Export\Factory as CollectionFactory;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper as ResourceHelper;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Customer
 * @package Isobar\OneFieldName\Override\Magento\CustomerImportExport\Model\Import
 */
class Customer extends BaseImportCustomer
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * Customer constructor.
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
     * @param AttributeCollectionFactory $attrCollectionFactory
     * @param CustomerFactory $customerFactory
     * @param array $data
     * @param Processor|null $indexerProcessor
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
        AttributeCollectionFactory $attrCollectionFactory,
        CustomerFactory $customerFactory,
        array $data = [],
        ?Processor $indexerProcessor = null
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
            $attrCollectionFactory,
            $customerFactory,
            $data,
            $indexerProcessor
        );
    }

    /**
     * @inheritDoc
     */
    protected function _validateRowForUpdate(array $rowData, $rowNumber)
    {
        if ($this->_checkUniqueKey($rowData, $rowNumber)) {
            $email = strtolower($rowData[self::COLUMN_EMAIL]);
            $website = $rowData[self::COLUMN_WEBSITE];

            if (isset($this->_newCustomers[strtolower($rowData[self::COLUMN_EMAIL])][$website])) {
                $this->addRowError(self::ERROR_DUPLICATE_EMAIL_SITE, $rowNumber);
            }
            $this->_newCustomers[$email][$website] = false;

            if (!empty($rowData[self::COLUMN_STORE]) && !isset($this->_storeCodeToId[$rowData[self::COLUMN_STORE]])) {
                $this->addRowError(self::ERROR_INVALID_STORE, $rowNumber);
            }
            // check password
            if (isset($rowData['password'])
                && strlen($rowData['password'])
                && $this->string->strlen($rowData['password']) < self::MIN_PASSWORD_LENGTH
            ) {
                $this->addRowError(self::ERROR_PASSWORD_LENGTH, $rowNumber);
            }
            // check simple attributes
            foreach ($this->_attributes as $attributeCode => $attributeParams) {
                if (in_array($attributeCode, $this->_ignoredAttributes)) {
                    continue;
                }

                /**#@+
                 * Override
                 */
                if ($attributeCode === CustomerInterface::LASTNAME
                    && $this->dataHelper->isShowOneFieldName($rowData[CustomerInterface::WEBSITE_ID])
                ) {
                    continue;
                }
                /**#@-*/

                $isFieldRequired = $attributeParams['is_required'];
                $isFieldNotSetAndCustomerDoesNotExist =
                    !isset($rowData[$attributeCode]) && !$this->_getCustomerId($email, $website);
                $isFieldSetAndTrimmedValueIsEmpty
                    = isset($rowData[$attributeCode]) && '' === trim((string)$rowData[$attributeCode]);

                if ($isFieldRequired && ($isFieldNotSetAndCustomerDoesNotExist || $isFieldSetAndTrimmedValueIsEmpty)) {
                    $this->addRowError(self::ERROR_VALUE_IS_REQUIRED, $rowNumber, $attributeCode);
                    continue;
                }

                if (isset($rowData[$attributeCode]) && strlen((string)$rowData[$attributeCode])) {
                    if ($attributeParams['type'] == 'select') {
                        continue;
                    }

                    $this->isAttributeValid(
                        $attributeCode,
                        $attributeParams,
                        $rowData,
                        $rowNumber,
                        isset($this->_parameters[Import::FIELD_FIELD_MULTIPLE_VALUE_SEPARATOR])
                            ? $this->_parameters[Import::FIELD_FIELD_MULTIPLE_VALUE_SEPARATOR]
                            : Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR
                    );
                }
            }
        }
    }
}
