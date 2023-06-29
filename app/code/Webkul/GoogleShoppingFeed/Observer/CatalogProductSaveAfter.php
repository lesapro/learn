<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Observer;

use Magento\Framework\Event\ObserverInterface;

class CatalogProductSaveAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory
     */
    private $googleFeedMap;

    /**
     * @var \Webkul\GoogleShoppingFeed\Helper\GoogleFeed $helperGoogleFeed
     */
    private $helperGoogleFeed;

    /**
     * @var \Webkul\GoogleShoppingFeed\Logger\Logger
     */
    private $logger;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configProTypeModel
     * @param \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory $googleFeedMap
     * @param \Webkul\GoogleShoppingFeed\Helper\GoogleFeed $helperGoogleFeed
     * @param \Webkul\GoogleShoppingFeed\Logger\Logger $logger
     */

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configProTypeModel,
        \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory $googleFeedMap,
        \Webkul\GoogleShoppingFeed\Helper\GoogleFeed $helperGoogleFeed,
        \Webkul\GoogleShoppingFeed\Logger\Logger $logger
    ) {
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->configProTypeModel = $configProTypeModel;
        $this->googleFeedMap = $googleFeedMap;
        $this->helperGoogleFeed = $helperGoogleFeed;
        $this->logger = $logger;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $accessToken = $this->helperGoogleFeed->helperData->getAccessToken();
            if ($accessToken) {
                $product = $observer->getProduct();
                $store = $this->storeManager->getStore();
                $storeDetailForFeed = $this->helperGoogleFeed->getStoreDetailForFeed($store);
                $googleFeedMap = $this->googleFeedMap->create()->getCollection()
                                        ->addFieldToFilter('mage_pro_id', ['eq'=> $product->getId()])
                                        ->addFieldToFilter('store_id', ['eq' => $store->getId()])
                                        ->setPageSize(1)->getFirstItem();
                if ($product->getTypeId() == 'configurable') {
                    $this->helperGoogleFeed->processConfigForExport(
                        $product,
                        $googleFeedMap,
                        $storeDetailForFeed,
                    );
                } else {
                    // insert/update feed on google shop
                    $availability = $product->getQuantityAndStockStatus();
                    $availability = isset($availability['qty']) && $availability['qty'] ? true : false;
                    if ($googleFeedMap->getEntityId() && $product->getStatus() == 1 && $availability
                        && $store->getIsActive() && in_array($store->getWebsiteId(), $productTmp->getWebsiteIds())) {
                        $storeDetailForFeed = $this->helperGoogleFeed->getStoreDetailForFeed($store);
                        $feedProduct = $this->helperGoogleFeed->getProductForFeed($product, $storeDetailForFeed);
                        $expireDate = $this->dateTime->gmtDate('Y-m-d H:i:s', strtotime("+30 days"));
                        $items = $this->helperGoogleFeed->insertFeedToGoogleShop($feedProduct);
                        if (($items['error'] == 0) && ($product->getVisibility() != 1)) {
                            $googleFeedMap->setFeedId($items['product']['id']);
                            $googleFeedMap->setExpiredAt($expireDate);
                            $googleFeedMap->save();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->addError('CatalogProductSaveAfter- : '. $e->getMessage());
            $this->messageManager->addNotice($e->getMessage());
        }
    }
}
