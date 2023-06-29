<?php

namespace Fovoso\ImportProducts\Model\Consumer;

use Fovoso\ImportProducts\Logger\Logger;

class ImportProducts
{

    protected $attributeOptions = [];

    protected $dataComment = [];

    protected $connection;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Source\Table
     */
    protected $attributeTable;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionInterface
     */
    protected $attributeOption;

    /**
     * @var \Magento\Eav\Api\AttributeOptionManagementInterface
     */
    protected $attributeOptionManagement;

    /**
     * @var \Fovoso\ImportProducts\Model\ProductNewUpdater
     */
    protected $productNewUpdate;

    /**
     * @var \Fovoso\ImportProducts\Model\ReviewUpdater
     */
    protected $reviewUpdater;

    public function __construct(
        Logger $logger,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Model\Entity\Attribute\Source\Table $attributeTable,
        \Magento\Eav\Api\Data\AttributeOptionInterface $attributeOption,
        \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement,
        \Fovoso\ImportProducts\Model\ProductNewUpdater $productNewUpdate,
        \Fovoso\ImportProducts\Model\ReviewUpdater $reviewUpdater
    ) {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->resourceConnection = $resourceConnection;
        $this->attributeRepository = $attributeRepository;
        $this->attributeTable = $attributeTable;
        $this->attributeOption = $attributeOption;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->productNewUpdate = $productNewUpdate;
        $this->reviewUpdater = $reviewUpdater;
    }

    protected function getConection()
    {
        if (!$this->connection) {
            $this->connection = $this->resourceConnection->getConnection();
        }
        return $this->connection;
    }

    public function process($data)
    {
        $this->logger->info('start import product');
        try {
            $imported = 0;
            foreach ($data as $product) {
                if (gettype($product) == "string") {
                    $product = $this->serializer->unserialize($product);
                }
                $this->logger->info('importing product: ' . $product['sku']);
                try {
                    $productData = $this->getProductData($product);
                    $this->validateAttributeOptions($this->attributeOptions);
                    // $this->attributeOptions = [];
                    $this->productNewUpdate->_saveProductsDataDup($productData);
                    if (!empty($this->dataComment)) {
                        $this->reviewUpdater->createReview($this->dataComment);
                    }
                    $imported++;
                } catch (\Exception $e) {
                    $this->logger->info('import ' . $product['sku'] . ' error: ' . $e->getMessage());
                }
            }
            $this->logger->info('imported ' . $imported . ' products');
        } catch (\Exception $e) {
            $this->logger->info('imported product error: ' . $e->getMessage());
        }
        $this->logger->info('end import product');
    }

    private function getProductData($product)
    {
        $variable = [];
        $categories = isset($product['categories']) ? $this->getCategory($product['categories']) : '';
        $shortDescription = $product['short_description'] ?? '';
        if (isset($product['child_products'])) {
            foreach ($product['child_products'] as $childProduct) {
                $productImportData[$childProduct['sku']] = [
                    'sku' => $childProduct['sku'],
                    "_store" => 0,
                    'store_view_code' => NULL,
                    'attribute_set_code' => 'Default',
                    'product_type' => 'simple',
                    'type_id' => 'simple',
                    'categories' => $categories,
                    'product_websites' => 'base',
                    'name' => $childProduct['name'],
                    'description' => $childProduct['description'],
                    'short_description' => $shortDescription,
                    'weight' => isset($childProduct['weight']) ? $childProduct['weight'] : "0.5",
                    "_attribute_set" => 'Default',
                    'product_online' => '1',
                    'tax_class_name' => 'No',
                    'visibility' => 'Not Visible Individually',
                    'price' => $childProduct['price'],
                    'sold_out' => $childProduct['sold_out'],
                    'qty' => $childProduct['qty'],
                    'out_of_stock_qty' => '0',
                    'use_config_min_qty' => '1',
                    'is_qty_decimal' => '0',
                    'allow_backorders' => '0',
                    'use_config_backorders' => '1',
                    'min_cart_qty' => '1',
                    'use_config_min_sale_qty' => 0,
                    'max_cart_qty' => '0',
                    'use_config_max_sale_qty' => '1',
                    'is_in_stock' => '1',
                    'notify_on_stock_below' => NULL,
                    'use_config_notify_stock_qty' => '1',
                    'manage_stock' => '0',
                    'use_config_manage_stock' => '1',
                    'use_config_qty_increments' => '1',
                    'qty_increments' => '0',
                    'use_config_enable_qty_inc' => '1',
                    'enable_qty_increments' => '0',
                    'is_decimal_divided' => '0',
                    'website_id' => '1',
                    'deferred_stock_update' => '0',
                    'use_config_deferred_stock_update' => '1',
                    'status' => '1',
                    'news_from_date' => NULL,
                    'news_to_date' => NULL,
                    'options_container' => 'Block after Info Column',
                    'minimal_price' => NULL,
                    'msrp' => NULL,
                    'msrp_enabled' => NULL,
                    'special_from_date' => NULL,
                    'special_to_date' => NULL,
                    'min_qty' => '0',
                    'backorders' => '0',
                    'min_sale_qty' => '1',
                    'max_sale_qty' => '0',
                    'notify_stock_qty' => NULL,
                    'quantity_and_stock_status' => 'In Stock',
                    'required_options' => '0',
                    '_product_websites' => 'base',
                    'thumbnail' => $childProduct['image'],
                    'image' => $childProduct['image'],
                    'small_image' => $childProduct['image'],
                    'additional_img' => $childProduct['additional_images'],
                ];
                if (isset($childProduct['special_price']) && !empty($childProduct['special_price'])) {
                    $productImportData[$childProduct['sku']]['special_price'] = $childProduct['special_price'];
                }
                $attributes = $childProduct['attributes'] ?? [];
                $stringVariable = '';
                $stringVariable .= 'sku=' . $childProduct['sku'] . ',';
                foreach ($attributes as $attributeCode => $attributeValue) {
                    if ($attributeValue) {
                        $productImportData[$childProduct['sku']][$attributeCode] = $attributeValue;
                        $stringVariable .= $attributeCode . '=' . $attributeValue . ',';
                        $this->attributeOptions[$attributeCode][] = $attributeValue;
                    }
                }
                $variable[] = rtrim($stringVariable, ',');
            }
        }

        $productImportData[] = [
            'sku' => $product['sku'],
            "_store" => 0,
            'brands' => $product['brands'] ?? '',
            'store_view_code' => NULL,
            'attribute_set_code' => 'Default',
            'product_type' => 'configurable',
            'type_id' => 'configurable',
            'categories' => $categories,
            'product_websites' => 'base',
            'name' => $product['name'],
            'description' => $product['description'],
            'short_description' => $product['short_description'],
            'weight' => isset($product['weight']) ? $product['weight'] : "0.5",
            "_attribute_set" => 'Default',
            'product_online' => '1',
            'tax_class_name' => 'No',
            'visibility' => "Catalog, Search",
            'price' => $product['price'],
            'sold_out' => $product['sold_out'],
            'shop' => isset($product['store']) ? $product['store'] : '',
            'qty' => $product['qty'],
            'out_of_stock_qty' => '0',
            'use_config_min_qty' => '1',
            'is_qty_decimal' => '0',
            'allow_backorders' => '0',
            'use_config_backorders' => '1',
            'min_cart_qty' => '1',
            'use_config_min_sale_qty' => 0,
            'max_cart_qty' => '0',
            'use_config_max_sale_qty' => '1',
            'is_in_stock' => '1',
            'notify_on_stock_below' => NULL,
            'use_config_notify_stock_qty' => '1',
            'manage_stock' => '0',
            'use_config_manage_stock' => '1',
            'use_config_qty_increments' => '1',
            'qty_increments' => '0',
            'use_config_enable_qty_inc' => '1',
            'enable_qty_increments' => '0',
            'is_decimal_divided' => '0',
            'website_id' => '1',
            'deferred_stock_update' => '0',
            'use_config_deferred_stock_update' => '1',
            'status' => '1',
            'news_from_date' => NULL,
            'news_to_date' => NULL,
            'options_container' => 'Block after Info Column',
            'minimal_price' => NULL,
            'msrp' => NULL,
            'msrp_enabled' => NULL,
            'special_from_date' => NULL,
            'special_to_date' => NULL,
            'min_qty' => '0',
            'backorders' => '0',
            'min_sale_qty' => '1',
            'max_sale_qty' => '0',
            'notify_stock_qty' => NULL,
            'quantity_and_stock_status' => 'In Stock',
            'required_options' => '0',
            'configurable_variations' => implode('|', $variable),
            '_product_websites' => 'base',
            'thumbnail' => $product['image'],
            'image' => $product['image'],
            'small_image' => $product['image'],
            'additional_img' => $product['additional_images'],
        ];

        if (isset($product['comments'])) {
            $this->dataComment[] = [
                'sku' => $product['sku'],
                'comments' => $product['comments'],
                'is_update' => $this->getProductBySku($product['sku'])
            ];
        }
        return $productImportData;
    }

    /**
     * @param $categoryName
     * @return array|string|string[]
     */
    private function getCategory($categoryName)
    {
        $categoryName = str_replace(' > ', '/', $categoryName);
        return 'Default Category/' . str_replace('>', '/', $categoryName);
    }

    /**
     * @param $sku
     * @return string
     */
    private function getProductBySku($sku)
    {
        $connection = $this->getConection();
        $productTable = $connection->getTableName('catalog_product_entity');
        $select = $connection->select()->from(
            $productTable,
            'entity_id'
        )->where('sku = ?', $sku);
        return $connection->fetchOne($select);
    }

    /**
     * @param $attributeOptions
     * @throws NoSuchEntityException
     */
    public function validateAttributeOptions($attributeOptions)
    {
        if (empty($attributeOptions)) {
            return;
        }
        foreach ($attributeOptions as $attributeCode => $attributeOption) {
            $attribute = $this->attributeRepository->get($attributeCode);
            $options = $this->getAllAttributeOptions($attribute);
            $newAttributeOptions = array_diff($attributeOption, $options);
            $valueIds = [];
            foreach ($newAttributeOptions as $newOption) {
                $optionId = $this->createAttributeOption($attribute, $newOption);
                $valueIds[] = [
                    'option_id' => $optionId,
                    'store_id' => 0,
                    'type' => 0,
                    'value' => $newOption
                ];
            }
            if (in_array($attributeCode, ['size', 'size_clothing', 'Size']) && !empty($valueIds)) {
                $connection = $this->getConection();
                $connection->insertOnDuplicate(
                    $connection->getTableName('eav_attribute_option_swatch'),
                    $valueIds
                );
            }
        }
    }

    private function getAllAttributeOptions($attribute)
    {
        $attribute->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $sourceModel = $this->attributeTable->setAttribute($attribute);
        $attrValues = [];
        foreach ($sourceModel->getAllOptions() as $option) {
            if ($option['label'] != '') {
                $attrValues[] = $option['label'];
            }
        }
        return $attrValues;
    }

    private function createAttributeOption($attribute, $attributeLabel)
    {
        $option = $this->attributeOption;
        $option->setLabel((string)$attributeLabel);
        $option->setValue((string)$attributeLabel);
        $option->setSortOrder(0);
        $option->setIsDefault(false);

        try {
            $this->attributeOptionManagement->add(
                \Magento\Catalog\Model\Product::ENTITY,
                $attribute->getAttributeCode(),
                $option
            );
            return $attribute->getSource()->getOptionId($attributeLabel);
        } catch (\Exception $e) {
            //            $this->messageManager->addErrorMessage("\nAttribute ".$attributeCode." ".$attributeLabel." create error or already create. SKIP");
        }
    }
}
