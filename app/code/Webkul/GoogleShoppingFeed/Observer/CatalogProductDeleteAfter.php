<?php
/**
 * Webkul Software.
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Webkul GoogleShoppingFeed CatalogProductDeleteAfterObserver Observer.
 */
class CatalogProductDeleteAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

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
     * @param \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory $googleFeedMap
     * @param \Webkul\GoogleShoppingFeed\Helper\GoogleFeed $helperGoogleFeed
     * @param \Webkul\GoogleShoppingFeed\Logger\Logger $logger
     */

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory $googleFeedMap,
        \Webkul\GoogleShoppingFeed\Helper\GoogleFeed $helperGoogleFeed,
        \Webkul\GoogleShoppingFeed\Logger\Logger $logger
    ) {
        $this->messageManager = $messageManager;
        $this->googleFeedMap = $googleFeedMap;
        $this->helperGoogleFeed = $helperGoogleFeed;
        $this->logger = $logger;
    }

    /**
     * Product delete after event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $accessToken = $this->helperGoogleFeed->helperData->getAccessToken();
            if ($accessToken) {
                $productId = $observer->getProduct()->getId();
                $googleFeedMap = $this->googleFeedMap->create()->getCollection()
                                                    ->addFieldToFilter('mage_pro_id', ['eq'=> $productId])
                                                    ->setPageSize(1)->getFirstItem();
                if ($googleFeedMap->getFeedId()) {
                    $items = $this->helperGoogleFeed->deleteFeedFromGoogleShop($googleFeedMap->getFeedId());
                    $googleFeedMap->delete();
                    $this->logger->addError(json_encode($items));
                }
            }
        } catch (\Exception $e) {
            $this->logger->addError('CatalogProductDeleteAfter : '. $e->getMessage());
            $this->messageManager->addError($e->getMessage());
        }
    }
}
