<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Helper;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache;
use Magento\Backend\Model\Session as CoreSession;
use Magento\Backend\Model\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Eav\Model\Entity as EavEntity;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory as AttrGroupCollection;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Webkul\GoogleShoppingFeed\Model\CategoryMapFactory;
use Webkul\GoogleShoppingFeed\Model\AttributeMapFactory;
use Webkul\GoogleShoppingFeed\Logger\Logger;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriter;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $cacheTypeList;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $cacheFrontendPool;

    /**
     * @var \Webkul\GoogleShoppingFeed\Model\AttributeMapFactory
     */
    private $attributeMap;

    /**
     * @var \Webkul\GoogleShoppingFeed\Logger\Logger
     */
    private $logger;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param UrlInterface $urlBuilder
     * @param EncryptorInterface $encryptor
     * @param TimezoneInterface $dateTime
     * @param \Magento\Config\Model\ResourceModel\Config $configWriter
     * @param Cache\TypeListInterface $cacheTypeList
     * @param Cache\Frontend\Pool $cacheFrontendPool
     * @param StoreManagerInterface $storeManager
     * @param EavEntity $eavEntity
     * @param AttrGroupCollection $attrGroupCollection
     * @param AttributeFactory $attributeFactory
     * @param CategoryMapFactory $categoryMap
     * @param AttributeMapFactory $attributeMap
     * @param Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        UrlInterface $urlBuilder,
        EncryptorInterface $encryptor,
        TimezoneInterface $dateTime,
        \Magento\Config\Model\ResourceModel\Config $configWriter,
        Cache\TypeListInterface $cacheTypeList,
        Cache\Frontend\Pool $cacheFrontendPool,
        StoreManagerInterface $storeManager,
        EavEntity $eavEntity,
        AttrGroupCollection $attrGroupCollection,
        AttributeFactory $attributeFactory,
        CategoryMapFactory $categoryMap,
        AttributeMapFactory $attributeMap,
        Logger $logger
    ) {
        parent::__construct($context);
        $this->urlBuilder = $urlBuilder;
        $this->encryptor = $encryptor;
        $this->dateTime = $dateTime;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->storeManager = $storeManager;
        $this->eavEntity = $eavEntity;
        $this->attrGroupCollection = $attrGroupCollection;
        $this->attributeFactory = $attributeFactory;
        $this->categoryMap = $categoryMap;
        $this->attributeMap = $attributeMap;
        $this->logger = $logger;
    }

    /**
     * Get ConfigDetails
     *
     * @return array
     */
    public function getConfigDetails()
    {
        $path = 'googleshoppingfeed/general_settings/';
        $googleAccountConfig = [
            'oauth_consumer_key' => $this->encryptor->decrypt($this->scopeConfig->getValue($path.'oauth_consumer_key')),
            'merchant_id' => $this->scopeConfig->getValue($path.'merchant_id'),
            'according_store' => $this->scopeConfig->getValue('googleshoppingfeed/default_config/according_store'),
            'category' => $this->scopeConfig->getValue('googleshoppingfeed/default_config/category'),
            'weight_unit' => $this->scopeConfig->getValue('googleshoppingfeed/default_config/weight_unit'),
            'oauth_consumer_secret' => $this->encryptor
                                            ->decrypt($this->scopeConfig->getValue($path.'oauth_consumer_secret')),
            'oauth2_access_token' => $this->encryptor
                                            ->decrypt($this->scopeConfig->getValue($path.'oauth2_access_token')),
            'oauth2_refresh_token' => $this->encryptor
                                            ->decrypt($this->scopeConfig->getValue($path.'oauth2_refresh_token')),
            'oauth2_access_token_expire_on' => $this->scopeConfig->getValue($path.'oauth2_access_token_expire_on')
        ];
        return $googleAccountConfig;
    }

    /**
     * Get OauthDetail Detail for Google Account.
     *
     * @return array of Google Account OauthDetail Detail
     */
    public function getOauthDetail()
    {
        try {
            $config = $this->getConfigDetails();
            $client = new \Google_Client();
            $client->setApplicationName('Google Shopping Feed');
            $client->setClientId($config['oauth_consumer_key']);
            $client->setClientSecret($config['oauth_consumer_secret']);
            $client->setRedirectUri($this->urlBuilder->getBaseUrl().'googleshoppingfeed/oauth/index/');
            $client->setScopes('https://www.googleapis.com/auth/content');
            $client->setAccessType("offline");
            $client->setApprovalPrompt("force");
            $oauthUrl = $client->createAuthUrl();
            return $oauthUrl;
        } catch (\Exception $e) {
            $this->logger->addError('getOauthDetail : '.$e->getMessage());
            $response = ['status' => false, 'error' => $e->getMessage()];
            return $response;
        }
    }

    /**
     * Clean ConfigCache
     *
     * @return void
     */
    public function cleanConfigCache()
    {
        $this->cacheTypeList->cleanType('config');
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }

    /**
     * Get Access Token;
     *
     * @return string|false
     */
    public function getAccessToken()
    {
        try {
            $config = $this->getConfigDetails();
            if ($config) {
                $expireDate = strtotime($config['oauth2_access_token_expire_on']);
                $currentDate = $this->dateTime->date();
                $currentDate = strtotime($this->dateTime->convertConfigTimeToUtc($currentDate));
                if ($currentDate > $expireDate) {
                    return false;
                }
                return $config['oauth2_access_token'];
            }
            return false;
        } catch (\Exception $e) {
            $this->logger->addError('getAccessToken - '.$e->getMessage());
            return false;
        }
    }

    /**
     * Refresh Access Token
     *
     * @return string
     */
    public function refreshAccessToken()
    {
        try {
            $config = $this->getConfigDetails();
            if ($config) {
                $client = new \Google_Client();
                $client->setClientId($config['oauth_consumer_key']);
                $client->setClientSecret($config['oauth_consumer_secret']);
                $result = $client->refreshToken($config['oauth2_refresh_token']);
                if (isset($result['access_token'])) {
                    $gmtCurrectTime = $this->dateTime->date();
                    $gmtCurrectTime = $this->dateTime->convertConfigTimeToUtc($gmtCurrectTime);
                    $accessTokenExpireOn = date(
                        'Y-m-d H:i:s',
                        strtotime('+'.($result['expires_in']-10).' seconds', strtotime($gmtCurrectTime))
                    );
                    $path = 'googleshoppingfeed/general_settings/';
                    $this->logger->addInfo('access_token :'.$result['access_token']);
                    $this->configWriter->saveConfig(
                        $path.'oauth2_access_token',
                        $this->encryptor->encrypt($result['access_token']),
                        'default',
                        0
                    );
                    $this->logger->addInfo('accessTokenExpireOn :'.$accessTokenExpireOn);
                    $this->configWriter->saveConfig(
                        $path.'oauth2_access_token_expire_on',
                        $accessTokenExpireOn,
                        'default',
                        0
                    );
                    $this->cleanConfigCache();
                    $this->logger->addInfo('refreshAccessToken - access token updated');
                    return $result['access_token'];
                } else {
                    $path = 'googleshoppingfeed/general_settings/';
                    $this->configWriter->saveConfig($path.'oauth2_access_token', null);
                    $this->configWriter->saveConfig($path.'oauth2_access_token_expire_on', null);
                    $this->cleanConfigCache();
                    $this->logger->addInfo('refreshAccessToken -expired');
                    return false;
                }
            }
        } catch (\Exception $e) {
            $this->logger->addError('refreshAccessToken - '.$e->getMessage());
            return false;
        }
    }

    /**
     * Get Mapped Category
     *
     * @param array $productCategories
     * @return string|false
     */
    public function getMappedCategory($productCategories)
    {
        $mappedCat = $this->categoryMap->create()->getCollection()
                                        ->addFieldToFilter('store_category_id', ['in' => $productCategories])
                                        ->setPageSize(1)->getFirstItem();
        $defaultCategory = $this->scopeConfig->getValue('googleshoppingfeed/default_config/category');
        $defaultCategory = $defaultCategory ? $defaultCategory : false;
        $googleProductCategory = $mappedCat->getEntityId() ? $mappedCat->getGoogleFeedCategory() : $defaultCategory;
        return $googleProductCategory;
    }

    /**
     * Get Product DataAsFieldMap
     *
     * @param Magento\Catalog\Model\Product $product
     * @param array $configProOptions
     * @return array $productData
     */
    public function getProductDataAsFieldMap($product, $configProOptions = [])
    {
        try {
            $mappedFields = $this->attributeMap->create()->getCollection();
            $productData = [];
            if (count($mappedFields) == 0) {
                $productData = [
                    'offerId' => 'sku',
                    'title' => 'name',
                    'description' => 'description',
                    'shippingWeight' => 'weight',
                    'brand' => 'manufacturer',
                    'mpn' => '',
                ];
            } else {
                foreach ($mappedFields as $field) {
                    $attribute = $product->getResource()->getAttribute($field->getAttributeCode());
                    if ($attribute) {
                        $attributeValue = $attribute->getFrontend()->getValue($product);
                        $productData[$field->getGoogleFeedField()] = $attributeValue;
                    }
                }
            }
            $path = 'googleshoppingfeed/default_config/';
            $ageGroup = $this->getAttributeValueFromProduct($product, 'gf_age_group');
            $ageGroup = $ageGroup != '' ? $ageGroup : $this->scopeConfig->getValue($path.'age_group');
            $productData['ageGroup'] = $ageGroup;

            $gender = $this->getAttributeValueFromProduct($product, 'gf_for_gender');
            $gender = $gender != '' ? $gender : $this->scopeConfig->getValue($path.'for_gender');
            $productData['gender'] = $gender;

            $condition = $this->getAttributeValueFromProduct($product, 'gf_condition');
            $condition = $condition != '' ? $condition : $this->scopeConfig->getValue($path.'condition');
            $productData['condition'] = $condition;

            $taxRate = $this->getAttributeValueFromProduct($product, 'gf_tax_rate');
            $taxRate = $taxRate != '' ? $taxRate : $this->scopeConfig->getValue($path.'tax_rate');
            $productData['taxRate'] = $taxRate;

            $taxOnShip = $this->getAttributeValueFromProduct($product, 'gf_tax_on_ship');
            $taxOnShip = $taxOnShip != '' ? $taxOnShip : $this->scopeConfig->getValue($path.'tax_on_ship');
            $productData['taxShip'] = $taxOnShip;
            foreach ($configProOptions as $options) {
                $optionValue = $this->getAttributeValueFromProduct($product, $options['attribute_code']);
                $productData[$options['attribute_code']] = $optionValue;
            }
            return $productData;
        } catch (\Exception $e) {
            $this->logger->addError('getProductDataAsFieldMap : '.$e->getMessage());
            return false;
        }
    }

    /**
     * Get AttributeValueFromProduct
     *
     * @param Magento\Catalog\Model\Product $product
     * @param string $attrCode
     * @return string
     */
    public function getAttributeValueFromProduct($product, $attrCode)
    {
        $path = 'googleshoppingfeed/default_config/';
        $attributeValue = '';
        $attribute = $product->getResource()->getAttribute($attrCode);
        if ($attribute) {
            $attributeValue = $product->getData($attrCode) ?  $attribute->getFrontend()->getValue($product) : '';
            if ($attrCode == "tax_on_ship") {
                $taxApply = $this->scopeConfig->getValue($path.'tax_apply');
                $attributeValue = $taxApply == 'global' ? $this->scopeConfig->getValue($path.$attrCode) :
                                                            $attributeValue;
            } else {
                $attributeValue = !in_array(strtolower($attributeValue), ['', 'select']) ?
                                        $attributeValue : $this->scopeConfig->getValue($path.$attrCode);
            }
        }
        return $attributeValue;
    }

    /**
     * Create ProductAttribute
     *
     * @param string $attrCode
     * @param string $type
     * @return string
     */
    public function createProductAttribute($attrCode, $type = 'varchar')
    {
        try {
            $input = 'text';
            $mageAttrCode = substr('gfield_'.strtolower($attrCode), 0, 30);
            $attributeInfo = $this->_getAttributeInfo($mageAttrCode);
            $allStores = $this->storeManager->getStores();
            $attributeSetId = 4;
            $attributeGroupId = $this->getAttributeGroupId('Google Shopping Feed Fields', $attributeSetId);
            if ($attributeInfo === false) {
                $attributeScope = \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE;
                $attribute = $this->attributeFactory->create();
                $attrLabel = ucwords(str_replace('_', '', $attrCode));
                $attrData = [
                    'entity_type_id' => $this->eavEntity->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId(),
                    'attribute_code' => $mageAttrCode,
                    'frontend_label' => [0 => $attrLabel],
                    'attribute_group_id' => $attributeGroupId,
                    'attribute_set_id' => $attributeSetId,
                    'backend_type' => $type,
                    'frontend_input' => $input,
                    'backend' => '',
                    'frontend' => '',
                    'source' => '',
                    'global' => $attributeScope,
                    'visible' => true,
                    'required' => false,
                    'is_user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_in_advanced_search' => false,
                    'unique' => false,
                ];

                $labels = [];
                foreach ($allStores as $store) {
                    $labels[$store->getId()] = $attrLabel;
                }
                $attribute->setData($attrData);
                $attribute->save();
            }
        } catch (\Exception $e) {
            $this->logger->addError('Create createProductAttribute : '.$e->getMessage());
            $mageAttrCode = null;
        }
        return $mageAttrCode;
    }

    /**
     * Get AttributeInfo
     *
     * @param string $mageAttrCode
     * @return false | Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    private function _getAttributeInfo($mageAttrCode)
    {
        $attributeInfoColl = $this->attributeFactory->create()->getCollection()
                                    ->addFieldToFilter('attribute_code', ['eq' => $mageAttrCode]);
        $attributeInfo = false;
        foreach ($attributeInfoColl as $attrInfoData) {
            $attributeInfo = $attrInfoData;
        }
        return $attributeInfo;
    }

    /**
     * Get AttributeGroupId
     *
     * @param string $groupName
     * @param int $attributeSetId
     * @return int
     */
    private function getAttributeGroupId($groupName, $attributeSetId)
    {
        $group = $this->attrGroupCollection->create()
                                        ->addFieldToFilter('attribute_group_name', $groupName)
                                        ->addFieldToFilter('attribute_set_id', $attributeSetId)
                                        ->setPageSize(1)->getFirstItem();
        if (!$group->getAttributeGroupId()) {
            $data = [
                'attribute_group_name' => $groupName,
                'attribute_set_id' => $attributeSetId
            ];
            $group = $group->setData($data)->save();
        }
        return $group->getId();
    }
}
