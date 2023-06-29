<?php

namespace Fovoso\ImportProducts\Model;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * Class ProductUpdater
 * @package Fovoso\ImportProducts\Model
 */
class ProductUpdater
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Source\Table
     */
    protected $attributeTable;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionInterface
     */
    protected $attributeOption;

    /**
     * @var \Magento\Eav\Api\AttributeOptionManagementInterface
     */
    protected $attributeOptionManagement;

    /**
     * @var \Magento\Catalog\Api\Data\ProductLinkInterface
     */
    protected $productLink;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $connection;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory
     */
    protected $configurableAttribute;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory
     */
    protected $configurableFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollection;

    protected $attribute = [];

    /**
     * ProductUpdater constructor.
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Eav\Model\Entity\Attribute\Source\Table $attributeTable
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param \Magento\Eav\Api\Data\AttributeOptionInterface $attributeOption
     * @param \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface $productLink
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\App\ResourceConnection $connection
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory $configurableAttribute
     * @param \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory $configurableFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Eav\Model\Entity\Attribute\Source\Table $attributeTable,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Api\Data\AttributeOptionInterface $attributeOption,
        \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement,
        \Magento\Catalog\Api\Data\ProductLinkInterface $productLink,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\ResourceConnection $connection,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory $configurableAttribute,
        \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory $configurableFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
    ) {
        $this->productFactory = $productFactory;
        $this->attributeTable = $attributeTable;
        $this->attributeRepository = $attributeRepository;
        $this->attributeOption = $attributeOption;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->productLink = $productLink;
        $this->productRepository = $productRepository;
        $this->connection = $connection;
        $this->configurableAttribute = $configurableAttribute;
        $this->configurableFactory = $configurableFactory;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->productCollection = $productCollection;
    }

    /**
     * @param $configurableData
     * @return array
     * @throws \Exception
     */
    public function createConfigurableProduct($configurableData)
    {
        $categories = $configurableData['categories'];
        $categoryIds = $this->createCategory($categories);
        $product = $this->productCollection->create()->addFieldToFilter('sku', $configurableData['sku'])->getFirstItem();
        $isUpdate = true;
        if (!$product->getId()) {
            $product = $this->productFactory->create();
            $isUpdate = false;
        }
        $product->setSku($configurableData['sku']);
        $product->setName($configurableData['name']);
        $product->setWeight(isset($configurableData['weight']) ? $configurableData['weight'] : 0.5);
        $product->setPrice($configurableData['price']);
        $product->setSpecialPrice($configurableData['special_price']);
        $product->setDescription($configurableData['description']);
        $product->setShortDescription($configurableData['short_description']);
        $product->setAttributeSetId(4);
        if ($categoryIds) {
            $product->setCategoryIds($categoryIds);
        }
        $product->setStatus(Status::STATUS_ENABLED);
        $product->setVisibility(Visibility::VISIBILITY_BOTH);
        $product->setTaxClassId(0);
        $product->setWebsiteIds([1]);
        $product->setTypeId('configurable');
        $product->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $product->setVisibility(4);
        $product->save();
        $this->importImages($configurableData['image'], $product->getId());
        $this->importAdditionalData($configurableData['additional_images'], $product->getId());
//        $this->insertShipping($product->getId(), $configurableData['shipping_fee']);
        return [$product->getId(), $isUpdate];
    }

    /**
     * @param $productId
     * @param $shippingFee
     */
    public function insertShipping($productId, $shippingFee)
    {
        if ($shippingFee) {
            $connection = $this->connection->getConnection();
            $connection->insertOnDuplicate(
                $this->connection->getTableName('fovoso_shipping_detail'),
                [
                    'product_id' => $productId,
                    'shipping_fee' => $shippingFee
                ]
            );
        }
    }

    /**
     * @param $categories
     * @return array|false
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createCategory($categories)
    {
        if (!empty($categories)) {
            $categories = explode('>', $categories);
            $cateIds = [];
            $position = 0;
            $parentId = $this->storeManager->getStore()->getRootCategoryId();
            $parentCategory = $this->categoryFactory->create()->load($parentId);
            $lastCate = null;
            foreach ($categories as $categoryName) {
                $categoryName = trim($categoryName);
                $category = $this->categoryFactory->create();
                $cate = $category->getCollection()
                    ->addAttributeToFilter('name', $categoryName)
                    ->getFirstItem();
                if ($position > 0) {
                    $parentId = $lastCate;
                }

                if (!$cate->getId()) {
                    $lastParentCate = $this->categoryFactory->create()->load($lastCate);
                    $parentPath = $parentCategory->getPath();
                    if ($position > 0) {
                        $parentPath = $lastParentCate->getPath();
                    }
                    $category->setPath($parentPath)
                        ->setParentId($parentId)
                        ->setName($categoryName)
                        ->setIncludeInMenu(true)
                        ->setIsActive(true);
                    $category->save();
                    $cateIds[] = $category->getId();
                    $lastCate = $category->getId();
                } else {
                    $cateIds[] = $cate->getId();
                    $lastCate = $cate->getId();
                }
                $position++;
            }
            return $cateIds;
        }
        return false;
    }

    /**
     * @param $imagePath
     * @param $productId
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function importImages($imagePath, $productId)
    {
        if (!$imagePath) {
            return true;
        }
        $imageAttributes = ['thumbnail', 'image', 'small_image', 'swatch_image'];
        $attributeData = [];
        foreach ($imageAttributes as $attributeCode) {
            $attribute = $this->attributeRepository->get($attributeCode);
            $attributeData[] = [
                'attribute_id' => $attribute->getAttributeId(),
                'store_id' => 0,
                'entity_id' => $productId,
                'value' => $imagePath
            ];
        }
        $this->connection->getConnection()->insertOnDuplicate(
            $this->connection->getTableName('catalog_product_entity_varchar'),
            $attributeData
        );
        return true;
    }

    /**
     * @param $imagePaths
     * @param $productId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function importAdditionalData($imagePaths, $productId)
    {
        if (!is_array($imagePaths) && !count($imagePaths)) {
            return;
        }
        $attribute = $this->getAttribute('media_gallery');
        $connection = $this->connection->getConnection();
        $position = 1;
        foreach ($imagePaths as $imagePath) {
            $galleryData = [
                'attribute_id' => $attribute->getId(),
                'value' => $imagePath,
                'media_type' => 'image',
                'disabled' => 0
            ];
            $connection->insertOnDuplicate(
                $this->connection->getTableName('catalog_product_entity_media_gallery'),
                $galleryData
            );
            $valueId = $connection->lastInsertId();
            $galleryValue = [
                'value_id' => $valueId,
                'store_id' => 0,
                'entity_id' => $productId,
                'position' => $position,
                'disabled' => 0
            ];
            $connection->insertOnDuplicate(
                $this->connection->getTableName('catalog_product_entity_media_gallery_value'),
                $galleryValue
            );
            $galleryToEntity = [
                'value_id' => $valueId,
                'entity_id' => $productId
            ];
            $connection->insertOnDuplicate(
                $this->connection->getTableName('catalog_product_entity_media_gallery_value_to_entity'),
                $galleryToEntity
            );
            $position++;
        }
    }

    /**
     * @param $simpleData
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createSimpleProduct($simpleData)
    {
        $product = $this->productCollection->create()->addFieldToFilter('sku', $simpleData['sku'])->getFirstItem();
        if (!$product->getId()) {
            $product = $this->productFactory->create();
        }
        $product->setSku($simpleData['sku']);
        $product->setName($simpleData['name']);
        $product->setAttributeSetId(4);
        $product->setStatus(Status::STATUS_ENABLED);
        $product->setWeight(isset($simpleData['weight']) ? $simpleData['weight'] : 0.5);
        $product->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE);
        $product->setTaxClassId(0);
        $product->setWebsiteIds([1]);
        $product->setTypeId('simple');
        $product->setPrice($simpleData['price']);
        $product->setSpecialPrice($simpleData['special_price']);
        if (!empty($simpleData['attributes'])) {
            foreach ($simpleData['attributes'] as $attributeCode => $attributeValue) {
                $attribute = $product->getResource()->getAttribute($attributeCode);
                if (!$attribute->getId()) {
                    continue;
                }
                $attributeOptions = $this->getAttributeOptions($attributeCode);
                $this->validateOptions($attributeCode, $attributeOptions, $attributeValue); // validate and create options
                $optionId = $attribute->getSource()->getOptionId($attributeValue);
                $product->setData($attributeCode, $optionId);
            }
        }
        $product->setStockData(
            [
                'use_config_manage_stock' => 0,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 99999999
            ]
        );
        $product->save();
        $this->importImages($simpleData['image'], $product->getId());
        $this->importAdditionalData($simpleData['additional_images'], $product->getId());
        return $product->getId();
    }

    /**
     * @param $parentId
     * @param array $childIds
     * @param $attributes
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function assignSimpleToConfigurable($parentId, array $childIds, $attributes)
    {
        $attributeModel = $this->configurableAttribute->create();
        foreach ($attributes as $key => $attributeId) {
            $data = [
                'attribute_id' => $attributeId,
                'product_id' => $parentId,
                'position' => $key
            ];
            $attributeModel->setData($data)->save();
        }
        $product = $this->productRepository->getById($parentId);
        $product->setAffectConfigurableProductAttributes(13);
        $this->configurableFactory->create()->setUsedProductAttributeIds($attributes, $product);
        $product->setAssociatedProductIds($childIds);
        $product->setCanSaveConfigurableAttributes(true);
        $product->save();
    }

    /**
     * Function check attribute from code and return attribute options values
     * @param $attributeCode
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttributeOptions($attributeCode)
    {
        $attribute = $this->attributeRepository->get($attributeCode);
        $attribute->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $sourceModel = $this->attributeTable->setAttribute($attribute);
        $attrValues = [];
        foreach ($sourceModel->getAllOptions() as $option) {
            $attrValues[$attribute->getAttributeId()][$option['label']] = $option['value'];
        }
        return $attrValues[$attribute->getAttributeId()];
    }

    /**
     * Validate attribute and create new attribute if not exists
     *
     * @param $attributeCode
     * @param $sourceOptions
     * @param $newOptions
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validateOptions($attributeCode, $sourceOptions, $newOption)
    {
        $sourceOptionsValue = array_keys($sourceOptions);
        if (!in_array($newOption, $sourceOptionsValue)) {
            if (trim($newOption) != '') {
                $this->createAttributeOption($attributeCode, $newOption);
            }
        }
        return true;
    }

    /**
     * Function create Attribute Options
     * @param $attributeCode
     * @param $attributeLabel
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    function createAttributeOption($attributeCode, $attributeLabel)
    {
        $attribute = $this->getAttribute($attributeCode);
        $option = $this->attributeOption;
        $option->setLabel((string)$attributeLabel);
        $option->setValue((string)$attributeLabel);
        $option->setSortOrder(0);
        $option->setIsDefault(false);

        try {
            $this->attributeOptionManagement->add(
                \Magento\Catalog\Model\Product::ENTITY,
                $attribute->getAttributeId(),
                $option
            );
            if (in_array($attributeCode, ['size', 'size_clothing', 'Size'])) {
                $optionId = $attribute->getSource()->getOptionId($attributeLabel);
                $this->connection->getConnection()->insert(
                    $this->connection->getTableName('eav_attribute_option_swatch'),
                    [
                        'option_id' => $optionId,
                        'store_id' => 0,
                        'type' => 0,
                        'value' => $attributeLabel
                    ]
                );
            }
        } catch (\Exception $e) {
//            $this->messageManager->addErrorMessage("\nAttribute ".$attributeCode." ".$attributeLabel." create error or already create. SKIP");
        }
    }

    /**
     * Function get attribute from code
     * @param $attributeCode
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttribute($attributeCode)
    {
        if (isset($this->attribute[$attributeCode])) {
            return $this->attribute[$attributeCode];
        }
        return $this->attribute[$attributeCode] = $this->attributeRepository->get($attributeCode);
    }
}
