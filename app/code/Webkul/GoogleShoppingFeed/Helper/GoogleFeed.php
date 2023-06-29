<?php

/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\CurrencyFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Webkul\GoogleShoppingFeed\Helper\Data as HelperData;
use Webkul\GoogleShoppingFeed\Logger\Logger;

class GoogleFeed extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Webkul\GoogleShoppingFeed\Helper\Data
     */
    public $helperData;

    /**
     * @var Webkul\GoogleShoppingFeed\Logger\Logger
     */
    private $logger;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param TimezoneInterface $dateTimeZone
     * @param StoreManagerInterface $storeManager
     * @param CurrencyFactory $currencyFactory
     * @param Configurable $configProTypeModel
     * @param ProductRepositoryInterface $productRepository
     * @param HelperData $helperData
     * @param Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        TimezoneInterface $dateTimeZone,
        DateTime $dateTime,
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory,
        Configurable $configProTypeModel,
        ProductRepositoryInterface $productRepository,
        HelperData $helperData,
        Logger $logger
    ) {
        parent::__construct($context);
        $this->dateTimeZone = $dateTimeZone;
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->configProTypeModel = $configProTypeModel;
        $this->productRepository = $productRepository;
        $this->helperData = $helperData;
        $this->logger = $logger;
        $this->helperData->getAccessToken();
    }

    /**
     * Insert FeedToGoogleShop
     *
     * @param Google_Service_ShoppingContent_Product $productFeed
     * @return Google_Service_ShoppingContent_Product
     */
    public function insertFeedToGoogleShop($productFeed)
    {
        try {
            $accessToken = $this->helperData->getAccessToken();
            if ($accessToken) {
                $config = $this->helperData->getConfigDetails();
                $client = new \Google_Client();
                $client->setAccessToken($config['oauth2_access_token']);
                $serviceShoppingContent =  new \Google_Service_ShoppingContent($client);
                $product = $serviceShoppingContent->products->insert($config['merchant_id'], $productFeed);
                return ['error' => 0, 'product' => $product];
            } else {
                return ['error' => 1, 'product' => null, 'message' => __('Google feed account not authenticated.')];
            }
        } catch (\Exception $e) {
            $this->logger->addError('postShopData : '.$e->getMessage());
            return ['error' => 1, 'product' => null, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get StoreDetailForFeed
     *
     * @param int $store
     * @return array
     */
    public function getStoreDetailForFeed($store = false)
    {
        try {
            $store = $store === false ? $this->storeManager->getStore() : $store;
            $locale =  $this->scopeConfig->getValue(
                'general/locale/code',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getStoreId()
            );
            $locale = explode('_', $locale);
            $storeDetails = [
                'base_media_url' => $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA),
                'base_currency' => $store->getBaseCurrencyCode(),
                'language' => $locale[0],
                'country' => $locale[1],
                'store_id' => $store->getId(),
                'currency_code' => $this->scopeConfig->getValue(
                    'currency/options/default',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store->getStoreId()
                )
            ];
            return $storeDetails;
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
    /**
     * Get PriceForConfigProduct
     *
     * @param Magento\Catalog\Model\product $product
     * @param array $storeDetail
     * @return float
     */
    private function getPriceForFeed($product, $storeDetail)
    {
        try {
            $price = $product->getPrice();
            if (in_array($product->getTypeId(), ['configurable', 'bundle', 'grouped'])) {
                $price = $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
            }
            $rate = $this->currencyFactory->create()->load($storeDetail['base_currency'])
                                            ->getAnyRate($storeDetail['currency_code']);
            return $price * $rate;
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * Get SalePriceForFeed
     *
     * @param Magento\Catalog\Model\product $product
     * @param array $storeDetail
     * @return float
     */
    private function getSalePriceForFeed($product, $storeDetail)
    {
        try {
            $price = $product->getPriceInfo()->getPrice('special_price')->getValue();
            if (in_array($product->getTypeId(), ['configurable', 'bundle', 'grouped'])) {
                $price = $product->getFinalPrice();
            }
            $rate = $this->currencyFactory->create()->load($storeDetail['base_currency'])
                                            ->getAnyRate($storeDetail['currency_code']);
            return $price * $rate;
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * Get ProductForFeed
     *
     * @param Magento\Catalog\Model\product $product
     * @param array $storeDetail
     * @param Magento\Catalog\Model\product $groupItem
     * @param array $configProOptions
     * @return \Google_Service_ShoppingContent_Product
     */
    public function getProductForFeed($product, $storeDetail, $groupItem = false, $configProOptions = [])
    {
        try {
            $googleProductCategory = $this->helperData->getMappedCategory($product->getCategoryIds());
            $cost = $this->getPriceForFeed($product, $storeDetail);
            $salePrice  = $this->getSalePriceForFeed($product, $storeDetail);
            $store = $this->storeManager->getStore($storeDetail['store_id']);
            $productData = $this->helperData->getProductDataAsFieldMap($product, $configProOptions);
            $feedProduct = new \Google_Service_ShoppingContent_Product();
            $feedProduct->setChannel("online");
            $feedProduct->setContentLanguage($storeDetail['language']);
            $feedProduct->setOfferId($productData['offerId']);
            $feedProduct->setTargetCountry($storeDetail['country']);
            $feedProduct->setTitle($productData['title']);
            $feedProduct->setDescription($productData['description']);
            $availability = $product->getQuantityAndStockStatus();
            $availability = isset($availability['qty']) && $availability['qty'] ? "in stock" : "out of stock";
            $feedProduct->setAvailability($availability);
            //$link = $store->getBaseUrl().$product->getUrlKey().'.html';
            //$link = $product->setStoreId($storeDetail['store_id'])->getUrlModel()
            //->getUrlInStore($product, ['_escape' => true]);
            $feedProduct->setLink($product->getProductUrl());
            if (isset($productData['brand']) && $productData['brand']) {
                $feedProduct->setBrand($productData['brand']);
            }
            if (isset($productData['color']) && $productData['color']) {
                $feedProduct->setColor($productData['color']);
            }
            if ($groupItem) {
                $feedProduct->setItemGroupId($groupItem->getSku());
                $link = $store->getBaseUrl().trim($groupItem->getUrlKey()).'.html';
                $feedProduct->setLink($link);
            }
            $feedProduct->setGender($productData['gender']);
            $customProduct = $product->getGfCustomProduct();
            if (isset($productData['gtin']) && !$customProduct) {
                $feedProduct->setGtin($productData['gtin']);
                /** $feedProduct->setMpn($productData['mpn']); */
            } else {
                $feedProduct->setIdentifierExists(false);
            }
            $feedProduct->setAgeGroup($productData['ageGroup']);
            if (isset($productData['sizes'])) {
                $feedProduct->setSizes($productData['sizes']);
            }
            if (isset($productData['sizeType'])) {
                $feedProduct->setSizes($productData['sizeType']);
            }
            if (isset($productData['sizeSystem'])) {
                $feedProduct->setSizeSystem($productData['sizeSystem']);
            }
            $imageLink =  isset($productData['imageLink']) && $productData['imageLink'] == 'no_selection' ?
                                '/placeholder/image.jpg' : $productData['imageLink'];
            $feedProduct->setImageLink($storeDetail['base_media_url'] . 'catalog/product' . $imageLink);
            $feedProduct->setCondition($productData['condition']);
            $feedProduct->setTitle($product->getName());

            $taxShip = $productData['taxShip'] == 'Yes'  ? true : false;
            $shipTaxGlobal = $this->scopeConfig->getValue('googleshoppingfeed/default_config/tax_on_ship');
            $shipApplied = $taxShip || ($productData['taxShip'] != 'No' && $shipTaxGlobal) ? true : false;
            $tax = new \Google_Service_ShoppingContent_ProductTax();
            $tax->setCountry($storeDetail['country']);
            $productData['taxRate'] = $productData['taxRate'] ? $productData['taxRate'] :
                $this->scopeConfig->getValue('googleshoppingfeed/default_config/tax_rate');
            $tax->setRate($productData['taxRate']);
            $tax->setTaxShip($shipApplied);
            $feedProduct->setTaxes([$tax]);
            if ($googleProductCategory) {
                $feedProduct->setGoogleProductCategory($googleProductCategory);
                /** $feedProduct->setProductTypes($googleProductCategory); */
            }
            if (isset($productData['shippingWeight']) && $productData['shippingWeight']) {
                $shippingWeight = new \Google_Service_ShoppingContent_ProductShippingWeight();
                $shippingWeight->setValue($productData['shippingWeight']);
                $shippingWeight->setUnit($this->scopeConfig->getValue('googleshoppingfeed/default_config/weight_unit'));
                $feedProduct->setShippingWeight($shippingWeight);
            }

            $price = new \Google_Service_ShoppingContent_Price();
            $price->setCurrency($storeDetail['currency_code']);
            $price->setValue($cost);
            $feedProduct->setPrice($price);
            if ($salePrice > 0) {
                $salePriceCost = new \Google_Service_ShoppingContent_Price();
                $salePriceCost->setCurrency($storeDetail['currency_code']);
                $salePriceCost->setValue($salePrice);
                $feedProduct->setSalePrice($salePriceCost);
                $date = $this->dateTimeZone->date(new \DateTime($product->getSpecialFromDate()))->format("Y-m-d")."/"
                        .$this->dateTimeZone->date(new \DateTime($product->getSpecialToDate()))->format("Y-m-d");
                $feedProduct->setSalePriceEffectiveDate($date);
            }
            return $feedProduct;
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * Delete FeedFromGoogleShop
     *
     * @param string $feedId
     * @param array $shopConfig
     */
    public function deleteFeedFromGoogleShop($feedId, $shopConfig = null)
    {
        try {
            $config = $shopConfig ? $shopConfig : $this->helperData->getConfigDetails();
            $client = new \Google_Client();
            $client->setAccessToken($config['oauth2_access_token']);
            $serviceShoppingContent =  new \Google_Service_ShoppingContent($client);
            $product = $serviceShoppingContent->products->delete($config['merchant_id'], $feedId);
            return $product;
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * Process ConfigForExport
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Webkul\GoogleShoppingFeed\Model\GoogleFeedMap $googleFeedMap
     * @param array $storeDetailForFeed
     * @return array
     */
    public function processConfigForExport($product, $googleFeedMap, $storeDetailForFeed)
    {
        try {
            $optionsDataList = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
            $associatePro = $this->configProTypeModel->getChildrenIds($product->getEntityId());
            if (isset($associatePro[0]) && !empty($associatePro[0])) {
                foreach ($associatePro[0] as $associateProId) {
                    $associateProduct = $this->productRepository->getById($associateProId);
                    $feedProduct = $this->getProductForFeed(
                        $associateProduct,
                        $storeDetailForFeed,
                        $product,
                        $optionsDataList
                    );
                    $items = $this->insertFeedToGoogleShop($feedProduct);
                    $expireDate = $this->dateTime->gmtDate('Y-m-d H:i:s', strtotime("+30 days"));
                    $syncsFeeds = [
                        'feed_id' => $items['product']['id'],
                        'mage_pro_id' => $product->getEntityId(),
                        'expired_at' => $expireDate,
                        'store_id' => $storeDetailForFeed['store_id']
                    ];
                    if (($items['error'] == 0) && $googleFeedMap->getEntityId()) {
                        $googleFeedMap->setExpiredAt($expireDate);
                        $googleFeedMap->setFeedId($items['product']['id']);
                    } else {
                        $googleFeedMap->setData($syncsFeeds)->save();
                    }
                    $googleFeedMap->save();
                }
            }
            $result = ['error' => 0, 'total' => 1];
        } catch (\Exception $e) {
            $this->logger->addError('processConfigForExport: '.$e->getMessage());
            $result = ['error' => 1, 'message' => $e->getMessage(), 'total' => 0];
        }
        return $result;
    }
}
