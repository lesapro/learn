<?php

namespace Fovoso\ImportProducts\Model;

use Fovoso\ImportProducts\Api\ImportProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;
/**
 * Class ImportProduct
 * @package Fovoso\ImportProducts\Model
 */
class ImportProduct implements ImportProductInterface
{
    /**
     * @var \Fovoso\ImportProducts\Model\ShippingFeeUpdater
     */
    protected $shippingFeeUpdater;

    /**
     * @var \Fovoso\ImportProducts\Model\ReviewUpdater
     */
    protected $reviewUpdater;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \Fovoso\ImportProducts\Logger\Logger
     */
    protected $logger;

    protected $attributeOptions = [];

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
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $connection;

    /**
     * ImportProduct constructor.
     * @param \Fovoso\ImportProducts\Model\ReviewUpdater $reviewUpdater
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Fovoso\ImportProducts\Logger\Logger $logger
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param \Magento\Eav\Model\Entity\Attribute\Source\Table $attributeTable
     * @param \Magento\Eav\Api\Data\AttributeOptionInterface $attributeOption
     * @param \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement
     * @param \Magento\Framework\App\ResourceConnection $connection
     * @param \Fovoso\ImportProducts\Model\ShippingFeeUpdater $shippingFeeUpdater
     */
    public function __construct(
        \Fovoso\ImportProducts\Model\ReviewUpdater $reviewUpdater,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Fovoso\ImportProducts\Logger\Logger $logger,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Model\Entity\Attribute\Source\Table $attributeTable,
        \Magento\Eav\Api\Data\AttributeOptionInterface $attributeOption,
        \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement,
        \Magento\Framework\App\ResourceConnection $connection,
        \Fovoso\ImportProducts\Model\ShippingFeeUpdater $shippingFeeUpdater
    ) {
        $this->reviewUpdater = $reviewUpdater;
        $this->request = $request;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->attributeRepository = $attributeRepository;
        $this->attributeTable = $attributeTable;
        $this->attributeOption = $attributeOption;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->connection = $connection;
        $this->shippingFeeUpdater = $shippingFeeUpdater;
    }

    /**
     * @return string|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->logger->critical(__('DA VAO DAY'));
        $userName = $this->request->getHeader('userName');
        $password = $this->request->getHeader('password');
        if ($userName != 'fovoso' || $password != 'fovoso123dfgr4') {
            $result = [
                'result' => false,
                'message' => 'User name or password is invalid'
            ];
            $this->logger->critical(__('User name or password is invalid'));
            return $this->serializer->serialize($result);
        }
        try {
            $data = $this->request->getContent();
            // $this->logger->info($data);
            $data = $this->serializer->unserialize($data);
            $productImportData = [];
            $dataComment = [];
            $dataShipping = [];
            foreach ($data as $dataChild) {
                $categories = $this->getCategory($dataChild['product']['categories']);
                $this->logger->info('Starting import product sku: '.$dataChild['product']['sku']);
                $variable = [];
                foreach ($dataChild['product']['child_products'] as $key => $childProduct) {
                    $stringVariable = '';
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
                        'short_description' => $dataChild['product']['short_description'],
                        'weight' => isset($childProduct['weight']) ? $childProduct['weight'] : 0.5,
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
                    $stringVariable .= 'sku=' . $childProduct['sku'] . ',';
                    foreach ($childProduct['attributes'] as $attributeCode => $attributeValue) {
                        if($attributeValue){
                            $productImportData[$childProduct['sku']][$attributeCode] = $attributeValue;
                            $stringVariable .= $attributeCode . '=' . $attributeValue . ',';
                            $this->attributeOptions[$attributeCode][] = $attributeValue;
                        }
                    }
                    $variable[] = rtrim($stringVariable, ',');
                }
                $brand =  $dataChild['product']['brands'] ?? '';
                $productImportData[] = [
                    'sku' => $dataChild['product']['sku'],
                    "_store" => 0,
                    'brands' => $brand,
                    'store_view_code' => NULL,
                    'attribute_set_code' => 'Default',
                    'product_type' => 'configurable',
                    'type_id' => 'configurable',
                    'categories' => $categories,
                    'product_websites' => 'base',
                    'name' => $dataChild['product']['name'],
                    'description' => $dataChild['product']['description'],
                    'short_description' => $dataChild['product']['short_description'],
                    'weight' => isset($dataChild['product']['weight']) ? $dataChild['product']['weight'] : 0.5,
                    "_attribute_set" => 'Default',
                    'product_online' => '1',
                    'tax_class_name' => 'No',
                    'visibility' => "Catalog, Search",
                    'price' => $dataChild['product']['price'],
                    'sold_out' => $dataChild['product']['sold_out'],
                    'shop' => isset($dataChild['product']['store']) ? $dataChild['product']['store'] : '',
                    'qty' => $dataChild['product']['qty'],
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
                    'thumbnail' => $dataChild['product']['image'],
                    'image' => $dataChild['product']['image'],
                    'small_image' => $dataChild['product']['image'],
                    'additional_img' => $dataChild['product']['additional_images'],
                ];
                if (isset($dataChild['comments'])) {
                    $dataComment[] = [
                        'sku' => $dataChild['product']['sku'],
                        'comments' => $dataChild['comments'],
                        'is_update' => $this->getProductBySku($dataChild['product']['sku'])
                    ];
                }
                if (isset($dataChild['product']['shipping'])) {
                    $shipping_meta= $this->serializer->serialize($dataChild['product']['shipping']);
                    $dataShipping[] = [
                        'sku' => $dataChild['product']['sku'] ?? '',
                        'brands' => $dataChild['product']['brands'],
                        'shipping_fee' => isset($dataChild['product']['shipping_fee']) ? $dataChild['product']['shipping_fee'] : 0,
                        'shipping_meta' => $shipping_meta
                    ];
                }elseif(isset($dataChild['product']['shippings'])) {
                    $shipping_meta= $this->serializer->serialize($dataChild['product']['shippings']); 
                    $dataShipping[] = [
                        'sku' => $dataChild['product']['sku'],
                        'brands' => $dataChild['product']['brands'] ?? '',
                        'shipping_fee' => isset($dataChild['product']['shipping_fee']) ? $dataChild['product']['shipping_fee'] : 0,
                        'shipping_meta' => $shipping_meta
                    ];
                }

            }
            $this->validateAttributeOptions($this->attributeOptions);
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Fovoso\ImportProducts\Model\ProductNewUpdater $productUpdater */
            $productUpdater = $objectManager->create(\Fovoso\ImportProducts\Model\ProductNewUpdater::class);
            $productUpdater->_saveProductsDataDup($productImportData);
            // Insert comment
            if (!empty($dataComment)) {
                $this->reviewUpdater->createReview($dataComment);
            }
            // Insert shipping
            if (!empty($dataShipping)) {
                $this->logger->info('Start import shipping');
                $this->shippingFeeUpdater->saveShipping($dataShipping, $this->logger);
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
        return $this->serializer->serialize(['result' => true]);
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
                $this->connection->getConnection()->insertOnDuplicate(
                    $this->connection->getTableName('eav_attribute_option_swatch'),
                    $valueIds
                );
            }
        }
    }

    /**
     * Function create Attribute Options
     * @param $attribute
     * @param $attributeLabel
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    function createAttributeOption($attribute, $attributeLabel)
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

    /**
     * @param $sku
     * @return string
     */
    public function getProductBySku($sku)
    {
        $select = $this->connection->getConnection()
            ->select()->from(
                $this->connection->getTableName('catalog_product_entity'),
                'entity_id'
            )->where('sku = ?', $sku);
        return $this->connection->getConnection()->fetchOne($select);
    }

    /**
     * Function check attribute from code and return attribute options values
     * @param $attribute
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllAttributeOptions($attribute)
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

    public function getDataAsJson()
    {
        return [
            [
                'product' => [
                    'sku' => 'TEST-01',
                    'name' => 'TEST PRODUCT 01',
                    'short_description' => '<p>product short description</p>',
                    'description' => '<p>product description</p>',
                    'price' => 123,
                    'image' => '/2/a/2a61533ece73ad3b6c1fcc4b4fc59c30.jpeg',
                    'shipping_fee' => 123,
                    'categories' => "Women's Clothing > Dresses > Mini",
                    'additional_images' => [
                        '/l/o/logo-fovoso.png',
                        '/d/e/demo10_03_1024x_3hr1hkwvrvrczf7q.jpg'
                    ],
                    'child_products' => [
                        [
                            'sku' => 'TEST-01-BLUE-X',
                            'name' => 'TEST PRODUCT 01 BLUE X',
                            'description' => '<p>product description 01</p>',
                            'price' => 124,
                            'image' => '/0/2/020a4c41d1afea2e3bb8260aa6d50b38.jpeg',
                            'additional_images' => [
                                '/l/o/logo-fovoso.png',
                                '/d/e/demo10_03_1024x_3hr1hkwvrvrczf7q.jpg'
                            ],
                            'attributes' => [
                                'color' => 'Blue',
                                'size'  => 'SS'
                            ]
                        ],
                        [
                            'sku' => 'TEST-01-BLUE-Xl',
                            'name' => 'TEST PRODUCT 01 BLUE Xl',
                            'description' => '<p>product description 02</p>',
                            'price' => 124,
                            'image' => '/d/e/demo10_03_1024x_3hr1hkwvrvrczf7q.jpg',
                            'additional_images' => [
                                '/l/o/logo-fovoso.png',
                                '/d/e/demo10_03_1024x_3hr1hkwvrvrczf7q.jpg'
                            ],
                            'attributes' => [
                                'color' => 'Blue',
                                'size'  => 'XXL'
                            ]
                        ]
                    ]
                ],
                'comments' => [
                    [
                        'nickName' => 'John',
                        'title' => 'Danh gia san pham xxx',
                        'detail' => 'Noi dung comment 666',
                        'rating' => '5',
                        'images' => [
                            '/0/2/020a4c41d1afea2e3bb8260aa6d50b38.jpeg',
                            '/0/2/020a4c41d1afea2e3bb8260aa6d50b38.jpeg'
                        ]
                    ],
                    [
                        'nickName' => 'Chien',
                        'title' => 'Danh gia san pham yyy',
                        'detail' => 'Noi dung comment ertfg',
                        'rating' => '4',
                        'images' => [
                            '/0/2/020a4c41d1afea2e3bb8260aa6d50b38.jpeg',
                            '/0/2/020a4c41d1afea2e3bb8260aa6d50b38.jpeg'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param $categoryName
     * @return array|string|string[]
     */
    public function getCategory($categoryName)
    {
        $categoryName = str_replace(' > ', '/', $categoryName);
        return 'Default Category/' . str_replace('>', '/', $categoryName);
    }
}
