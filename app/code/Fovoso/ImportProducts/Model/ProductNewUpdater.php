<?php

namespace Fovoso\ImportProducts\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogImportExport\Model\Import\Product;
use Magento\CatalogImportExport\Model\Import\Product\MediaGalleryProcessor;
use Magento\CatalogImportExport\Model\Import\Product\StockProcessor;
use Magento\CatalogImportExport\Model\StockItemImporterInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Stdlib\DateTime;
use Magento\ImportExport\Model\Import;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\Config as CatalogConfig;

/**
 * Class ProductNewUpdater
 * @package Fovoso\ImportProducts\Model
 */
class ProductNewUpdater extends \Magento\CatalogImportExport\Model\Import\Product
{
    /**
     * Url key attribute code
     */
    const URL_KEY = 'url_key';

    /**
     * Attribute cache
     *
     * @var array
     */
    protected $_attributeCache = [];

    /**
     * Column product name.
     */
    const COL_NAME = 'name';

    /**
     * Column product store.
     */
    const COL_STORE = '_store';

    /**
     * Column product attribute set.
     */
    const COL_ATTR_SET = '_attribute_set';

    /**
     * Column names that holds values with particular meaning.
     *
     * @var string[]
     */
    protected $_specialAttributes = [
        self::COL_STORE,
        self::COL_ATTR_SET,
        self::COL_TYPE,
        self::COL_CATEGORY,
        '_product_websites',
        self::COL_PRODUCT_WEBSITES,
        '_tier_price_website',
        '_tier_price_customer_group',
        '_tier_price_qty',
        '_tier_price_price',
        '_related_sku',
        '_related_position',
        '_crosssell_sku',
        '_crosssell_position',
        '_upsell_sku',
        '_upsell_position',
        '_custom_option_store',
        '_custom_option_type',
        '_custom_option_title',
        '_custom_option_is_required',
        '_custom_option_price',
        '_custom_option_sku',
        '_custom_option_max_characters',
        '_custom_option_sort_order',
        '_custom_option_file_extension',
        '_custom_option_image_size_x',
        '_custom_option_image_size_y',
        '_custom_option_row_title',
        '_custom_option_row_price',
        '_custom_option_row_sku',
        '_custom_option_row_sort',
        '_media_attribute_id',
        '_media_label',
        '_media_position',
        '_media_is_disabled',
    ];

    /**
     * Column product sku.
     */
    const COL_SKU = 'sku';

    /**
     * Array of supported product types as keys with appropriate model object as value.
     *
     * @var \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType[]
     */
    protected $_productTypeModels = [
        'simple' => 'Magento\CatalogImportExport\Model\Import\Product\Type\Simple'
    ];

    /**
     * Column product category.
     */
    const COL_CATEGORY = 'categories';

    /**
     * Column product type.
     */
    const COL_TYPE = 'product_type';

    /**
     * Data row scopes.
     */
    const SCOPE_DEFAULT = 1;

    const SCOPE_WEBSITE = 2;

    const SCOPE_STORE = 0;

    const SCOPE_NULL = -1;

    /**
     * Entity model parameters.
     *
     * @var array
     */
    protected $_parameters = [];

    /**
     * Existing products SKU-related information in form of array:
     *
     * [SKU] => array(
     *     'type_id'        => (string) product type
     *     'attr_set_id'    => (int) product attribute set ID
     *     'entity_id'      => (int) product ID
     *     'supported_type' => (boolean) is product type supported by current version of import module
     * )
     *
     * @var array
     */
    protected $_oldSku = [];

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModel
     */
    protected $_resource;

    /**
     * Product metadata pool
     *
     * @var \Magento\Framework\EntityManager\MetadataPool
     * @since 100.1.0
     */
    protected $metadataPool;

    /**
     * Attributes codes which shows as date
     *
     * @var array
     * @since 100.1.2
     */
    protected $dateAttrCodes = [
        'special_from_date',
        'special_to_date',
        'news_from_date',
        'news_to_date',
        'custom_design_from',
        'custom_design_to'
    ];

    /**
     * @var array
     */
    protected $defaultStockData = [
        'manage_stock' => 1,
        'use_config_manage_stock' => 1,
        'qty' => 0,
        'min_qty' => 0,
        'use_config_min_qty' => 1,
        'min_sale_qty' => 1,
        'use_config_min_sale_qty' => 1,
        'max_sale_qty' => 10000,
        'use_config_max_sale_qty' => 1,
        'is_qty_decimal' => 0,
        'backorders' => 0,
        'use_config_backorders' => 1,
        'notify_stock_qty' => 1,
        'use_config_notify_stock_qty' => 1,
        'enable_qty_increments' => 0,
        'use_config_enable_qty_inc' => 1,
        'qty_increments' => 0,
        'use_config_qty_increments' => 1,
        'is_in_stock' => 1,
        'low_stock_date' => null,
        'stock_status_changed_auto' => 0,
        'is_decimal_divided' => 0,
    ];

    /**
     * @var array
     */
    protected $websitesCache = [];

    /**
     * Column product website.
     */
    const COL_PRODUCT_WEBSITES = '_product_websites';

    protected $_data = [];

    protected $isBunch;

    /**
     * Map between import file fields and system fields/attributes.
     *
     * @var array
     */
    protected $_fieldsMap = [
        'image' => 'base_image',
        'image_label' => "base_image_label",
        'thumbnail' => 'thumbnail_image',
        'thumbnail_label' => 'thumbnail_image_label',
        '_media_image_label' => 'additional_image_labels',
        '_media_is_disabled' => 'hide_from_product_page',
        Product::COL_STORE => 'store_view_code',
        Product::COL_ATTR_SET => 'attribute_set_code',
        Product::COL_TYPE => 'product_type',
        Product::COL_PRODUCT_WEBSITES => 'product_websites',
        'status' => 'product_online',
        'news_from_date' => 'new_from_date',
        'news_to_date' => 'new_to_date',
        'options_container' => 'display_product_options_in',
        'minimal_price' => 'map_price',
        'msrp' => 'msrp_price',
        'msrp_enabled' => 'map_enabled',
        'special_from_date' => 'special_price_from_date',
        'special_to_date' => 'special_price_to_date',
        'min_qty' => 'out_of_stock_qty',
        'backorders' => 'allow_backorders',
        'min_sale_qty' => 'min_cart_qty',
        'max_sale_qty' => 'max_cart_qty',
        'notify_stock_qty' => 'notify_on_stock_below',
        '_related_sku' => 'related_skus',
        '_related_position' => 'related_position',
        '_crosssell_sku' => 'crosssell_skus',
        '_crosssell_position' => 'crosssell_position',
        '_upsell_sku' => 'upsell_skus',
        '_upsell_position' => 'upsell_position',
        'meta_keyword' => 'meta_keywords',
    ];

    /**
     * @var array
     */
    protected $categoriesCache = [];

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor
     */
    protected $categoryProcessor;

    /**
     * Product entity identifier field
     *
     * @var string
     */
    private $productEntityIdentifierField;

    /**
     * @var \Magento\Catalog\Model\Product\Url
     * @since 100.0.3
     */
    protected $productUrl;

    /**
     * @var Product\StoreResolver
     */
    protected $storeResolver;

    /**
     * @var CatalogConfig
     */
    protected $catalogConfig;

    /**
     * @var Product\SkuProcessor
     */
    protected $skuProcessor;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Product\TaxClassProcessor
     */
    protected $taxClassProcessor;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Proxy\ProductFactory
     */
    protected $_proxyProdFactory;

    /**
     * Product entity link field
     *
     * @var string
     */
    private $productEntityLinkField;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory
     */
    protected $_resourceFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var Product\StatusProcessor
     */
    protected $statusProcessor;

    /**
     * @var Product\LinkProcessor
     */
    protected $linkProcessor;

    /**
     * @var \Magento\CatalogInventory\Api\StockConfigurationInterface
     */
    protected $stockConfiguration;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface
     */
    protected $stockStateProvider;

    /**
     * @var DateTimeFactory
     */
    protected $dateTimeFactory;

    /**
     * @var StockItemImporterInterface
     */
    protected $stockItemImporter;

    /**
     * @var StockProcessor
     */
    protected $stockProcessor;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * Entity type id.
     *
     * @var int
     */
    protected $_entityTypeId = 4;

    protected $additionalImages = [];

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    protected $_setColFactory;

    /**
     * Pairs of attribute set name-to-ID.
     *
     * @var array
     */
    protected $_attrSetNameToId = [];

    /**
     * Pairs of attribute set ID-to-name.
     *
     * @var array
     */
    protected $_attrSetIdToName = [];

    /**
     * @var Import\Config
     */
    protected $_importConfig;

    /**
     * @var Product\Type\Factory
     */
    protected $_productTypeFactory;

    /**
     * Provide ability to process and save images during import.
     *
     * @var MediaGalleryProcessor
     */
    private $mediaProcessor;

    /**
     * ProductNewUpdater constructor.
     * @param Product\CategoryProcessor $categoryProcessor
     * @param \Magento\Catalog\Model\Product\Url $productUrl
     * @param Product\StoreResolver $storeResolver
     * @param CatalogConfig $catalogConfig
     * @param Product\SkuProcessor $skuProcessor
     * @param ProductRepositoryInterface $productRepository
     * @param Product\TaxClassProcessor $taxClassProcessor
     * @param \Magento\CatalogImportExport\Model\Import\Proxy\ProductFactory $proxyProdFactory
     * @param \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory $_resourceFactory
     * @param DateTime $dateTime
     * @param DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\ResourceConnection $connection
     * @param Product\StatusProcessor $statusProcessor
     * @param Product\LinkProcessor $linkProcessor
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider
     * @param DateTimeFactory $dateTimeFactory
     * @param StockItemImporterInterface $stockItemImporter
     * @param StockProcessor $stockProcessor
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $_setColFactory
     * @param Import\Config $importConfig
     * @param Product\Type\Factory $productTypeFactory
     * @param MediaGalleryProcessor $mediaProcessor
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param \Magento\Eav\Model\Entity\Attribute\Source\Table $attributeTable
     * @param \Magento\Eav\Api\Data\AttributeOptionInterface $attributeOption
     * @param \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement
     * @throws LocalizedException
     */
    public function __construct(
        \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor $categoryProcessor,
        \Magento\Catalog\Model\Product\Url $productUrl,
        \Magento\CatalogImportExport\Model\Import\Product\StoreResolver $storeResolver,
        CatalogConfig $catalogConfig,
        \Magento\CatalogImportExport\Model\Import\Product\SkuProcessor $skuProcessor,
        ProductRepositoryInterface $productRepository,
        \Magento\CatalogImportExport\Model\Import\Product\TaxClassProcessor $taxClassProcessor,
        \Magento\CatalogImportExport\Model\Import\Proxy\ProductFactory $proxyProdFactory,
        \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory $_resourceFactory,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\ResourceConnection $connection,
        \Magento\CatalogImportExport\Model\Import\Product\StatusProcessor $statusProcessor,
        \Magento\CatalogImportExport\Model\Import\Product\LinkProcessor $linkProcessor,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider,
        DateTimeFactory $dateTimeFactory,
        StockItemImporterInterface $stockItemImporter,
        StockProcessor $stockProcessor,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $_setColFactory,
        \Magento\ImportExport\Model\Import\Config $importConfig,
        \Magento\CatalogImportExport\Model\Import\Product\Type\Factory $productTypeFactory,
        MediaGalleryProcessor $mediaProcessor
    ) {
        $this->categoryProcessor = $categoryProcessor;
        $this->productUrl = $productUrl;
        $this->storeResolver = $storeResolver;
        $this->catalogConfig = $catalogConfig;
        $this->skuProcessor = $skuProcessor;
        $this->productRepository = $productRepository;
        $this->taxClassProcessor = $taxClassProcessor;
        $this->_proxyProdFactory = $proxyProdFactory;
        $this->_resourceFactory = $_resourceFactory;
        $this->dateTime = $dateTime;
        $this->_localeDate = $localeDate;
        $this->_eventManager = $eventManager;
        $this->_connection = $connection->getConnection();
        $this->statusProcessor = $statusProcessor;
        $this->linkProcessor = $linkProcessor;
        $this->stockConfiguration = $stockConfiguration;
        $this->stockRegistry = $stockRegistry;
        $this->stockStateProvider = $stockStateProvider;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->stockItemImporter = $stockItemImporter;
        $this->stockProcessor = $stockProcessor;
        $this->indexerRegistry = $indexerRegistry;
        $this->_setColFactory = $_setColFactory;
        $this->_importConfig = $importConfig;
        $this->_productTypeFactory = $productTypeFactory;
        $this->mediaProcessor = $mediaProcessor;
        $this->_initAttributeSets()
            ->_initTypeModels()
            ->_initSkus();
    }

    /**
     * Initialize existent product SKUs.
     *
     * @return $this
     */
    protected function _initSkus()
    {
        $this->skuProcessor->setTypeModels($this->_productTypeModels);
        $this->_oldSku = $this->skuProcessor->reloadOldSkus()->getOldSkus();
        return $this;
    }

    /**
     * Initialize attribute sets code-to-id pairs.
     *
     * @return $this
     */
    protected function _initAttributeSets()
    {
        foreach ($this->_setColFactory->create()->setEntityTypeFilter($this->_entityTypeId) as $attributeSet) {
            $this->_attrSetNameToId[$attributeSet->getAttributeSetName()] = $attributeSet->getId();
            $this->_attrSetIdToName[$attributeSet->getId()] = $attributeSet->getAttributeSetName();
        }
        return $this;
    }

    /**
     * Initialize product type models.
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _initTypeModels()
    {
        $productTypes = $this->_importConfig->getEntityTypes($this->getEntityTypeCode());
        $fieldsMap = [];
        $specialAttributes = [];
        foreach ($productTypes as $productTypeName => $productTypeConfig) {
            if (!in_array($productTypeName, ['simple', 'configurable'])) {
                continue;
            }
            $params = [$this, $productTypeName];
            if (!($model = $this->_productTypeFactory->create($productTypeConfig['model'], ['params' => $params]))
            ) {
                throw new LocalizedException(
                    __('Entity type model \'%1\' is not found', $productTypeConfig['model'])
                );
            }
            if (!$model instanceof \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType) {
                throw new LocalizedException(
                    __(
                        'Entity type model must be an instance of '
                        . \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType::class
                    )
                );
            }
            if ($model->isSuitable()) {
                $this->_productTypeModels[$productTypeName] = $model;
            }
            $fieldsMap[] = $model->getCustomFieldsMapping();
            $specialAttributes[] = $model->getParticularAttributes();
        }
        $this->_fieldsMap = array_merge([], $this->_fieldsMap, ...$fieldsMap);
        // remove doubles
        $this->_specialAttributes = array_unique(array_merge([], $this->_specialAttributes, ...$specialAttributes));

        return $this;
    }

    /**
     * EAV entity type code getter.
     *
     * @abstract
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'catalog_product';
    }

    /**
     * Save products data.
     *
     * @return $this
     * @throws LocalizedException
     */
    public function _saveProductsDataDup($bunch)
    {
        $this->_data = $bunch;
        $this->_saveProductsDup($bunch);
        foreach ($this->_productTypeModels as $productTypeModel) {
            $productTypeModel->saveData();
        }
        $this->_saveStockItemDup($bunch);

        return $this;
    }

    /**
     * Returns TRUE if row is valid and not in skipped rows array.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function isRowAllowedToImport(array $rowData, $rowNum)
    {
        return true;
    }

    /**
     * Check if row is invalid by row number
     *
     * @param int $rowNumber
     * @return bool
     */
    public function isRowInvalid($rowNumber)
    {
        return true;
    }

    /**
     * Get error aggregator object
     *
     * @return $this
     */
    public function getErrorAggregator()
    {
        return $this;
    }

    /**
     * Get next bunch of validated rows.
     *
     * @return array|null
     */
    public function getNextBunch()
    {
        if ($this->isBunch) {
            return [];
        }
        $this->isBunch = true;
        return $this->_data;
    }

    /**
     * Stock item saving.
     *
     * @return $this
     */
    protected function _saveStockItemDup($bunch)
    {
        $stockData = [];
        $productIdsToReindex = [];
        $stockChangedProductIds = [];
        // Format bunch to stock data rows
        foreach ($bunch as $rowNum => $rowData) {
            $row = [];
            $sku = $rowData[self::COL_SKU];
            if ($this->skuProcessor->getNewSku($sku) !== null) {
                $stockItem = $this->getRowExistingStockItem($rowData);
                $existingStockItemData = $stockItem->getData();
                $row = $this->formatStockDataForRow($rowData);
                $productIdsToReindex[] = $row['product_id'];
                $storeId = $this->getRowStoreId($rowData);
                if (!empty(array_diff_assoc($row, $existingStockItemData))
                    || $this->statusProcessor->isStatusChanged($sku, $storeId)
                ) {
                    $stockChangedProductIds[] = $row['product_id'];
                }
            }

            if (!isset($stockData[$sku])) {
                $stockData[$sku] = $row;
            }
        }

        // Insert rows
        if (!empty($stockData)) {
            $this->stockItemImporter->import($stockData);
        }

        $this->reindexStockStatus($stockChangedProductIds);
        $this->reindexProducts($productIdsToReindex);
        return $this;
    }

    /**
     * Reindex stock status for provided product IDs
     *
     * @param array $productIds
     */
    private function reindexStockStatus(array $productIds): void
    {
        if ($productIds) {
            $this->stockProcessor->reindexList($productIds);
        }
    }

    /**
     * Initiate product reindex by product ids
     *
     * @param array $productIdsToReindex
     * @return void
     */
    private function reindexProducts($productIdsToReindex = [])
    {
        $indexer = $this->indexerRegistry->get('catalog_product_category');
        if (is_array($productIdsToReindex) && count($productIdsToReindex) > 0 && !$indexer->isScheduled()) {
            $indexer->reindexList($productIdsToReindex);
        }
    }

    /**
     * Get row store ID
     *
     * @param array $rowData
     * @return int
     */
    private function getRowStoreId(array $rowData): int
    {
        return !empty($rowData[self::COL_STORE])
            ? (int) $this->getStoreIdByCode($rowData[self::COL_STORE])
            : Store::DEFAULT_STORE_ID;
    }

    /**
     * Format row data to DB compatible values.
     *
     * @param array $rowData
     * @return array
     */
    private function formatStockDataForRow(array $rowData): array
    {
        $sku = $rowData[self::COL_SKU];
        $row['product_id'] = $this->skuProcessor->getNewSku($sku)['entity_id'];
        $row['website_id'] = $this->stockConfiguration->getDefaultScopeId();
        $row['stock_id'] = $this->stockRegistry->getStock($row['website_id'])->getStockId();

        $stockItemDo = $this->stockRegistry->getStockItem($row['product_id'], $row['website_id']);
        $existStockData = $stockItemDo->getData();

        if (isset($rowData['qty']) && $rowData['qty'] == 0 && !isset($rowData['is_in_stock'])) {
            $rowData['is_in_stock'] = 0;
        }

        $row = array_merge(
            $this->defaultStockData,
            array_intersect_key($existStockData, $this->defaultStockData),
            array_intersect_key($rowData, $this->defaultStockData),
            $row
        );

        if ($this->stockConfiguration->isQty($this->skuProcessor->getNewSku($sku)['type_id'])) {
            if (isset($rowData['qty']) && $rowData['qty'] == 0) {
                $row['is_in_stock'] = 0;
            }
            $stockItemDo->setData($row);
            $row['is_in_stock'] = $row['is_in_stock'] ?? $this->stockStateProvider->verifyStock($stockItemDo);
            if ($this->stockStateProvider->verifyNotification($stockItemDo)) {
                $date = $this->dateTimeFactory->create('now', new \DateTimeZone('UTC'));
                $row['low_stock_date'] = $date->format(DateTime::DATETIME_PHP_FORMAT);
            }
            $row['stock_status_changed_auto'] = (int)!$this->stockStateProvider->verifyStock($stockItemDo);
        } else {
            $row['qty'] = 0;
        }

        return $row;
    }

    /**
     * Get row stock item model
     *
     * @param array $rowData
     * @return StockItemInterface
     */
    private function getRowExistingStockItem(array $rowData): StockItemInterface
    {
        $productId = $this->skuProcessor->getNewSku($rowData[self::COL_SKU])['entity_id'];
        $websiteId = $this->stockConfiguration->getDefaultScopeId();
        return $this->stockRegistry->getStockItem($productId, $websiteId);
    }

    public function _saveProductsDup($bunch)
    {
        $productLimit = null;
        $productsQty = null;
        $entityLinkField = 'entity_id';

        $entityRowsIn = [];
        $entityRowsUp = [];
        $attributes = [];
        $this->categoriesCache = [];
        $this->websitesCache = [];
        $previousType = null;
        $prevAttributeSet = null;

        foreach ($bunch as $rowNum => $rowData) {
            if (!$this->validateRow($rowData, $rowNum)) {
                continue;
            }
            // reset category processor's failed categories array
            $this->categoryProcessor->clearFailedCategories();
            $rowScope = 0;
            $urlKey = $this->getUrlKey($rowData);
            if (!empty($rowData[self::URL_KEY])) {
                // If url_key column and its value were in the CSV file
                $rowData[self::URL_KEY] = $urlKey;
            } elseif ($this->isNeedToChangeUrlKey($rowData)) {
                // If url_key column was empty or even not declared in the CSV file but by the rules it is need to
                // be setteed. In case when url_key is generating from name column we have to ensure that the bunch
                // of products will pass for the event with url_key column.
                $bunch[$rowNum][self::URL_KEY] = $rowData[self::URL_KEY] = $urlKey;
            }
            $rowSku = $rowData[self::COL_SKU];
            $rowSkuNormalized = mb_strtolower($rowSku);
            $storeId = !empty($rowData[self::COL_STORE])
                ? $this->getStoreIdByCode($rowData[self::COL_STORE])
                : Store::DEFAULT_STORE_ID;
            // 1. Entity phase
            if ($this->isSkuExist($rowSku)) {
                // existing row
                if (isset($rowData['attribute_set_code'])) {
                    $attributeSetId = $this->catalogConfig->getAttributeSetId(
                        4,
                        'Default'
                    );
                } else {
                    $attributeSetId = $this->skuProcessor->getNewSku($rowSku)['attr_set_id'];
                }

                $entityRowsUp[] = [
                    'updated_at' => (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT),
                    'attribute_set_id' => $attributeSetId,
                    $entityLinkField => $this->getExistingSku($rowSku)[$entityLinkField]
                ];
            } else {
                if (!$productLimit || $productsQty < $productLimit) {
                    $entityRowsIn[strtolower($rowSku)] = [
                        'attribute_set_id' => $this->skuProcessor->getNewSku($rowSku)['attr_set_id'],
                        'type_id' => $this->skuProcessor->getNewSku($rowSku)['type_id'],
                        'sku' => $rowSku,
                        'has_options' => isset($rowData['has_options']) ? $rowData['has_options'] : 0,
                        'created_at' => (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT),
                        'updated_at' => (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT),
                    ];
                    $productsQty++;
                } else {
                    $rowSku = null;
                    // sign for child rows to be skipped
                    continue;
                }
            }

            if (!array_key_exists($rowSku, $this->websitesCache)) {
                $this->websitesCache[$rowSku] = [];
            }
            // 2. Product-to-Website phase
            if (!empty($rowData[self::COL_PRODUCT_WEBSITES])) {
                $websiteCodes = explode($this->getMultipleValueSeparator(), $rowData[self::COL_PRODUCT_WEBSITES]);
                foreach ($websiteCodes as $websiteCode) {
                    $websiteId = $this->storeResolver->getWebsiteCodeToId($websiteCode);
                    $this->websitesCache[$rowSku][$websiteId] = true;
                }
            } else {
                $product = $this->retrieveProductBySku($rowSku);
                if ($product) {
                    $websiteIds = $product->getWebsiteIds();
                    foreach ($websiteIds as $websiteId) {
                        $this->websitesCache[$rowSku][$websiteId] = true;
                    }
                }
            }

            // 3. Categories phase
            if (!array_key_exists($rowSku, $this->categoriesCache)) {
                $this->categoriesCache[$rowSku] = [];
            }
            $rowData['rowNum'] = $rowNum;
            $categoryIds = $this->processRowCategories($rowData);
            foreach ($categoryIds as $id) {
                $this->categoriesCache[$rowSku][$id] = true;
            }
            unset($rowData['rowNum']);
            // 5. Media gallery phase

            // 6. Attributes phase
            $rowStore = 0;
            $productType = isset($rowData[self::COL_TYPE]) ? $rowData[self::COL_TYPE] : null;
            if ($productType !== null) {
                $previousType = $productType;
            }
            if (isset($rowData[self::COL_ATTR_SET])) {
                $prevAttributeSet = $rowData[self::COL_ATTR_SET];
            }
            if (self::SCOPE_NULL == $rowScope) {
                // for multiselect attributes only
                if ($prevAttributeSet !== null) {
                    $rowData[self::COL_ATTR_SET] = $prevAttributeSet;
                }
                if ($productType === null && $previousType !== null) {
                    $productType = $previousType;
                }
                if ($productType === null) {
                    continue;
                }
            }

            $productTypeModel = $this->_productTypeModels[$productType];
            if (isset($rowData['tax_class_name']) && strlen($rowData['tax_class_name'])) {
                $rowData['tax_class_id'] =
                    $this->taxClassProcessor->upsertTaxClass($rowData['tax_class_name'], $productTypeModel);
            }
            // Pharse 4: Main image
            if (!empty($rowData['additional_img'])) {
                $position = 2;
                $this->additionalImages[$rowSku][$rowData['image']] = [
                    'attribute_id' => $this->getMediaGalleryAttributeId(),
                    'label' => '',
                    'position' => 1,
                    'disabled' => '0',
                    'value' => $rowData['image'],
                ];
                foreach ($rowData['additional_img'] as $ad_img) {
                    $this->additionalImages[$rowSku][$ad_img] = [
                        'attribute_id' => $this->getMediaGalleryAttributeId(),
                        'label' => '',
                        'position' => $position,
                        'disabled' => '0',
                        'value' => $ad_img,
                    ];
                    $position++;
                }
            }
            $rowData = $productTypeModel->clearEmptyData($rowData);

            $rowData = $productTypeModel->prepareAttributesWithDefaultValueForSave(
                $rowData,
                !$this->isSkuExist($rowSku)
            );
            $product = $this->_proxyProdFactory->create(['data' => $rowData]);

            foreach ($rowData as $attrCode => $attrValue) {
                $attribute = $this->retrieveAttributeByCode($attrCode);

                if ('multiselect' != $attribute->getFrontendInput() && self::SCOPE_NULL == $rowScope) {
                    // skip attribute processing for SCOPE_NULL rows
                    continue;
                }
                $attrId = $attribute->getId();
                $backModel = $attribute->getBackendModel();
                $attrTable = $attribute->getBackend()->getTable();
                $storeIds = [0];

                if ('datetime' == $attribute->getBackendType()
                    && (
                        in_array($attribute->getAttributeCode(), $this->dateAttrCodes)
                        || $attribute->getIsUserDefined()
                    )
                ) {
                    $attrValue = $this->dateTime->formatDate($attrValue, false);
                } elseif ('datetime' == $attribute->getBackendType() && strtotime($attrValue)) {
                    $attrValue = gmdate(
                        'Y-m-d H:i:s',
                        $this->_localeDate->date($attrValue)->getTimestamp()
                    );
                } elseif ($backModel) {
                    $attribute->getBackend()->beforeSave($product);
                    $attrValue = $product->getData($attribute->getAttributeCode());
                }
                if (self::SCOPE_STORE == $rowScope) {
                    if (self::SCOPE_WEBSITE == $attribute->getIsGlobal()) {
                        // check website defaults already set
                        if (!isset($attributes[$attrTable][$rowSku][$attrId][$rowStore])) {
                            $storeIds = $this->storeResolver->getStoreIdToWebsiteStoreIds($rowStore);
                        }
                    } elseif (self::SCOPE_STORE == $attribute->getIsGlobal()) {
                        $storeIds = [$rowStore];
                    }
                    if (!$this->isSkuExist($rowSku)) {
                        $storeIds[] = 0;
                    }
                }
                foreach ([0] as $storeId) {
                    if (!isset($attributes[$attrTable][$rowSku][$attrId][$storeId])) {
                        $attributes[$attrTable][$rowSku][$attrId][$storeId] = $attrValue;
                    }
                }
                // restore 'backend_model' to avoid 'default' setting
                $attribute->setBackendModel($backModel);
            }
        }

        $this->saveProductEntity($entityRowsIn, $entityRowsUp)
            ->_saveProductWebsites($this->websitesCache)
            ->_saveProductCategories($this->categoriesCache)
            ->_saveMediaGallery([$this->additionalImages])
            ->_saveProductAttributes($attributes);

        $this->_eventManager->dispatch(
            'catalog_product_import_bunch_save_after',
            ['adapter' => $this, 'bunch' => $bunch]
        );
    }

    /**
     * Save product media gallery.
     *
     * @param array $mediaGalleryData
     * @return $this
     */
    protected function _saveMediaGallery(array $mediaGalleryData)
    {
        if (empty($mediaGalleryData)) {
            return $this;
        }
        $this->mediaProcessor->saveMediaGallery($mediaGalleryData);

        return $this;
    }

    /**
     * Add message template for specific error code from outside.
     *
     * @param string $errorCode Error code
     * @param string $message Message template
     * @return $this
     */
    public function addMessageTemplate($errorCode, $message)
    {
        return $this;
    }

    /**
     * @return int
     */
    public function getEntityTypeId()
    {
        return 4;
    }

    /**
     * Save product attributes.
     *
     * @param array $attributesData
     * @return $this
     */
    protected function _saveProductAttributes(array $attributesData)
    {
        $linkField = $this->getProductEntityLinkField();
        $statusAttributeId = (int) $this->retrieveAttributeByCode('status')->getId();
        foreach ($attributesData as $tableName => $skuData) {
            $linkIdBySkuForStatusChanged = [];
            $tableData = [];
            foreach ($skuData as $sku => $attributes) {
                $linkId = $this->_oldSku[strtolower($sku)][$linkField];
                foreach ($attributes as $attributeId => $storeValues) {
                    foreach ($storeValues as $storeId => $storeValue) {
                        if ($attributeId === $statusAttributeId) {
                            $this->statusProcessor->setStatus($sku, $storeId, $storeValue);
                            $linkIdBySkuForStatusChanged[strtolower($sku)] = $linkId;
                        }
                        $tableData[] = [
                            $linkField => $linkId,
                            'attribute_id' => $attributeId,
                            'store_id' => $storeId,
                            'value' => $storeValue,
                        ];
                    }
                }
            }
            if ($linkIdBySkuForStatusChanged) {
                $this->statusProcessor->loadOldStatus($linkIdBySkuForStatusChanged);
            }
            $this->_connection->insertOnDuplicate($tableName, $tableData, ['value']);
        }

        return $this;
    }

    /**
     * Save product categories.
     *
     * @param array $categoriesData
     * @return $this
     */
    protected function _saveProductCategories(array $categoriesData)
    {
        static $tableName = null;

        if (!$tableName) {
            $tableName = $this->_resourceFactory->create()->getProductCategoryTable();
        }
        if ($categoriesData) {
            $categoriesIn = [];
            $delProductId = [];

            foreach ($categoriesData as $delSku => $categories) {
                $productId = $this->skuProcessor->getNewSku($delSku)['entity_id'];
                $delProductId[] = $productId;

                foreach (array_keys($categories) as $categoryId) {
                    $categoriesIn[] = ['product_id' => $productId, 'category_id' => $categoryId, 'position' => 0];
                }
            }
//            if (Import::BEHAVIOR_APPEND != $this->getBehavior()) {
//                $this->_connection->delete(
//                    $tableName,
//                    $this->_connection->quoteInto('product_id IN (?)', $delProductId)
//                );
//            }
            if ($categoriesIn) {
                $this->_connection->insertOnDuplicate($tableName, $categoriesIn, ['product_id', 'category_id']);
            }
        }
        return $this;
    }

    /**
     * Validate data row.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return boolean
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Zend_Validate_Exception
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $sku = $rowData[self::COL_SKU];

        // SKU is specified, row is SCOPE_DEFAULT, new product block begins
        $this->_processedEntitiesCount++;

        if ($this->isSkuExist($sku)) {
            // can we get all necessary data from existent DB product?
            // check for supported type of existing product
            if (isset($this->_productTypeModels[$this->getExistingSku($sku)['type_id']])) {
                $this->skuProcessor->addNewSku(
                    $sku,
                    $this->prepareNewSkuData($sku)
                );
            }
        } elseif ($this->skuProcessor->getNewSku($sku) === null) {
            $this->skuProcessor->addNewSku(
                $sku,
                [
                    'row_id' => null,
                    'entity_id' => null,
                    'type_id' => $rowData[self::COL_TYPE],
                    'attr_set_id' => $this->_attrSetNameToId[$rowData[self::COL_ATTR_SET]],
                    'attr_set_code' => $rowData[self::COL_ATTR_SET],
                ]
            );
        }
        return true;
    }

    /**
     * Prepare new SKU data
     *
     * @param string $sku
     * @return array
     */
    private function prepareNewSkuData($sku)
    {
        $data = [];
        foreach ($this->getExistingSku($sku) as $key => $value) {
            $data[$key] = $value;
        }

        $data['attr_set_code'] = $this->_attrSetIdToName[$this->getExistingSku($sku)['attr_set_id']];

        return $data;
    }

    /**
     * Save product websites.
     *
     * @param array $websiteData
     * @return $this
     */
    protected function _saveProductWebsites(array $websiteData)
    {
        static $tableName = null;

        if (!$tableName) {
            $tableName = $this->_resourceFactory->create()->getProductWebsiteTable();
        }
        if ($websiteData) {
            $websitesData = [];
            $delProductId = [];

            foreach ($websiteData as $delSku => $websites) {
                $productId = $this->skuProcessor->getNewSku($delSku)['entity_id'];
                $delProductId[] = $productId;

                foreach (array_keys($websites) as $websiteId) {
                    $websitesData[] = ['product_id' => $productId, 'website_id' => $websiteId];
                }
            }
//            if (Import::BEHAVIOR_APPEND != $this->getBehavior()) {
//                $this->_connection->delete(
//                    $tableName,
//                    $this->_connection->quoteInto('product_id IN (?)', $delProductId)
//                );
//            }
            if ($websitesData) {
                $this->_connection->insertOnDuplicate($tableName, $websitesData);
            }
        }
        return $this;
    }

    /**
     * Update and insert data in entity table.
     *
     * @param array $entityRowsIn Row for insert
     * @param array $entityRowsUp Row for update
     * @return $this
     * @since 100.1.0
     */
    public function saveProductEntity(array $entityRowsIn, array $entityRowsUp)
    {
        static $entityTable = null;

        if (!$entityTable) {
            $entityTable = $this->_resourceFactory->create()->getEntityTable();
        }
        if ($entityRowsUp) {
            $this->_connection->insertOnDuplicate($entityTable, $entityRowsUp, ['updated_at', 'attribute_set_id']);
        }
        if ($entityRowsIn) {
            $this->_connection->insertMultiple($entityTable, $entityRowsIn);

            $select = $this->_connection->select()->from(
                $entityTable,
                array_merge($this->getNewSkuFieldsForSelect(), $this->getOldSkuFieldsForSelect())
            )->where(
                $this->_connection->quoteInto('sku IN (?)', array_keys($entityRowsIn))
            );
            $newProducts = $this->_connection->fetchAll($select);
            foreach ($newProducts as $data) {
                $sku = $data['sku'];
                unset($data['sku']);
                foreach ($data as $key => $value) {
                    $this->skuProcessor->setNewSkuData($sku, $key, $value);
                }
            }

            $this->updateOldSku($newProducts);
        }

        return $this;
    }

    /**
     * Adds newly created products to _oldSku
     *
     * @param array $newProducts
     * @return void
     */
    private function updateOldSku(array $newProducts)
    {
        $oldSkus = [];
        foreach ($newProducts as $info) {
            $typeId = $info['type_id'];
            $sku = strtolower($info['sku']);
            $oldSkus[$sku] = [
                'type_id' => $typeId,
                'attr_set_id' => $info['attribute_set_id'],
                $this->getProductIdentifierField() => $info[$this->getProductIdentifierField()],
                'supported_type' => isset($this->_productTypeModels[$typeId]),
                $this->getProductEntityLinkField() => $info[$this->getProductEntityLinkField()],
            ];
        }

        $this->_oldSku = array_replace($this->_oldSku, $oldSkus);
    }

    /**
     * Return additional data, needed to select.
     *
     * @return array
     */
    private function getOldSkuFieldsForSelect()
    {
        return ['type_id', 'attribute_set_id'];
    }

    /**
     * Get new SKU fields for select
     *
     * @return array
     */
    private function getNewSkuFieldsForSelect()
    {
        $fields = ['sku', $this->getProductEntityLinkField()];
        if ($this->getProductEntityLinkField() != $this->getProductIdentifierField()) {
            $fields[] = $this->getProductIdentifierField();
        }
        return $fields;
    }

    /**
     * Get product entity identifier field
     *
     * @return string
     */
    private function getProductIdentifierField()
    {
        if (!$this->productEntityIdentifierField) {
            $this->productEntityIdentifierField = $this->getMetadataPool()
                ->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class)
                ->getIdentifierField();
        }
        return $this->productEntityIdentifierField;
    }

    /**
     * Get product entity link field
     *
     * @return string
     */
    private function getProductEntityLinkField()
    {
        if (!$this->productEntityLinkField) {
            $this->productEntityLinkField = $this->getMetadataPool()
                ->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class)
                ->getLinkField();
        }
        return $this->productEntityLinkField;
    }

    /**
     * Get product metadata pool
     *
     * @return \Magento\Framework\EntityManager\MetadataPool
     * @since 100.1.0
     */
    protected function getMetadataPool()
    {
        if (!$this->metadataPool) {
            $this->metadataPool = ObjectManager::getInstance()
                ->get(\Magento\Framework\EntityManager\MetadataPool::class);
        }
        return $this->metadataPool;
    }

    /**
     * Retrieve url key from provided row data.
     *
     * @param array $rowData
     * @return string
     *
     * @since 100.0.3
     */
    protected function getUrlKey($rowData)
    {
        if (!empty($rowData[self::URL_KEY])) {
            $urlKey = (string) $rowData[self::URL_KEY];
            return trim(strtolower($urlKey));
        }

        if (!empty($rowData[self::COL_NAME])
            && (array_key_exists(self::URL_KEY, $rowData) || !$this->isSkuExist($rowData[self::COL_SKU]))) {
            return $this->productUrl->formatUrlKey($rowData[self::COL_NAME]);
        }

        return '';
    }

    /**
     * Check if product exists for specified SKU
     *
     * @param string $sku
     * @return bool
     */
    private function isSkuExist($sku)
    {
        $sku = strtolower($sku);
        return isset($this->_oldSku[$sku]);
    }

    /**
     * Whether a url key is needed to be change.
     *
     * @param array $rowData
     * @return bool
     */
    private function isNeedToChangeUrlKey(array $rowData): bool
    {
        $urlKey = $this->getUrlKey($rowData);
        $productExists = $this->isSkuExist($rowData[self::COL_SKU]);
        $markedToEraseUrlKey = isset($rowData[self::URL_KEY]);
        // The product isn't new and the url key index wasn't marked for change.
        if (!$urlKey && $productExists && !$markedToEraseUrlKey) {
            // Seems there is no need to change the url key
            return false;
        }

        return true;
    }

    /**
     * Get store id by code.
     *
     * @param string $storeCode
     * @return array|int|null|string
     */
    public function getStoreIdByCode($storeCode)
    {
        if (empty($storeCode)) {
            return self::SCOPE_DEFAULT;
        }
        return $this->storeResolver->getStoreCodeToId($storeCode);
    }

    /**
     * Get existing product data for specified SKU
     *
     * @param string $sku
     * @return array
     */
    private function getExistingSku($sku)
    {
        return $this->_oldSku[strtolower($sku)];
    }

    /**
     * Multiple value separator getter.
     *
     * @return string
     */
    public function getMultipleValueSeparator()
    {
        if (!empty($this->_parameters[Import::FIELD_FIELD_MULTIPLE_VALUE_SEPARATOR])) {
            return $this->_parameters[Import::FIELD_FIELD_MULTIPLE_VALUE_SEPARATOR];
        }
        return Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR;
    }

    /**
     * Retrieve product by sku.
     *
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    private function retrieveProductBySku($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
        } catch (NoSuchEntityException $e) {
            return null;
        }
        return $product;
    }

    /**
     * Resolve valid category ids from provided row data.
     *
     * @param array $rowData
     * @return array
     */
    protected function processRowCategories($rowData)
    {
        $categoriesString = empty($rowData[self::COL_CATEGORY]) ? '' : $rowData[self::COL_CATEGORY];
        $categoryIds = [];
        if (!empty($categoriesString)) {
            $categoryIds = $this->categoryProcessor->upsertCategories(
                $categoriesString,
                $this->getMultipleValueSeparator()
            );
        } else {
            $product = $this->retrieveProductBySku($rowData['sku']);
            if ($product) {
                $categoryIds = $product->getCategoryIds();
            }
        }
        return $categoryIds;
    }

    /**
     * Retrieve attribute by code
     *
     * @param string $attrCode
     * @return mixed
     */
    public function retrieveAttributeByCode($attrCode)
    {
        /** @var string $attrCode */
        $attrCode = mb_strtolower($attrCode);

        if (!isset($this->_attributeCache[$attrCode])) {
            $this->_attributeCache[$attrCode] = $this->getResource()->getAttribute($attrCode);
        }

        return $this->_attributeCache[$attrCode];
    }

    /**
     * Retrieve resource.
     *
     * @return \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModel
     *
     * @since 100.0.3
     */
    protected function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = $this->_resourceFactory->create();
        }
        return $this->_resource;
    }
}
