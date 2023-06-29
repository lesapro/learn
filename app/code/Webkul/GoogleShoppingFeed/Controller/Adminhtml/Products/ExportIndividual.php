<?php
/**
 * GoogleShoppingFeed Admin Product Create Controller.
 * @category  Webkul
 * @package   Webkul_GoogleShoppingFeed
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Controller\Adminhtml\Products;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Json\Helper\Data as JsonHelperData;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProTypeModel;
use Webkul\GoogleShoppingFeed\Helper\GoogleFeed as HelperGoogleFeed;
use Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory;

class ExportIndividual extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelperData;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Webkul\GoogleShoppingFeed\Helper\GoogleFeed
     */
    private $helperGoogleFeed;

    /**
     * @var \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory
     */
    private $feedMap;

    /**
     * @param Context $context
     * @param JsonHelperData $jsonHelperData
     * @param StoreRepositoryInterface $storeRepository
     * @param DateTime $dateTime
     * @param ConfigurableProTypeModel $configProTypeModel
     * @param ProductRepositoryInterface $productRepository
     * @param HelperGoogleFeed $helperGoogleFeed
     * @param GoogleFeedMapFactory $feedMap
     */
    public function __construct(
        Context $context,
        JsonHelperData $jsonHelperData,
        StoreRepositoryInterface $storeRepository,
        DateTime $dateTime,
        ConfigurableProTypeModel $configProTypeModel,
        ProductRepositoryInterface $productRepository,
        HelperGoogleFeed $helperGoogleFeed,
        GoogleFeedMapFactory $feedMap
    ) {
        parent::__construct($context);
        $this->jsonHelperData = $jsonHelperData;
        $this->storeRepository = $storeRepository;
        $this->dateTime = $dateTime;
        $this->configProTypeModel = $configProTypeModel;
        $this->productRepository = $productRepository;
        $this->helperGoogleFeed = $helperGoogleFeed;
        $this->feedMap = $feedMap;
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        try {
            $result = $this->validateData();
            if (isset($result['requestData']) && !empty($result['requestData'])) {
                $requestData = $result['requestData'];
                $configDetails = $this->helperGoogleFeed->helperData->getConfigDetails();
                $storeList = $configDetails['according_store'] == 1 ?
                    $this->storeRepository->getList() : [$this->storeRepository->getById(0)];
                foreach ($storeList as $store) {
                    if ($store->getId() || $configDetails['according_store'] == 0) {
                        $storeDetailForFeed = $this->helperGoogleFeed->getStoreDetailForFeed($store);
                        $mappedPro = $this->feedMap->create()->getCollection()
                                        ->addFieldToFilter('mage_pro_id', ['eq' => $requestData['product']])
                                        ->addFieldToFilter('store_id', ['eq' => $storeDetailForFeed['store_id']])
                                        ->setPageSize(1)->getFirstItem();
                        $product = $this->productRepository->getById(
                            $requestData['product'],
                            false,
                            $storeDetailForFeed['store_id']
                        );
                        if ($product->getTypeId() == 'configurable') {
                            $result = $this->helperGoogleFeed->processConfigForExport(
                                $product,
                                $mappedPro,
                                $storeDetailForFeed
                            );
                        } else {
                            $availability = $product->getQuantityAndStockStatus();
                            $availability = isset($availability['qty']) && $availability['qty'] ? true : false;
                            $productWebsId = $product->getWebsiteIds();
                            array_push($productWebsId, 0);
                            if ($product->getStatus() == 1 && $availability && $store->getIsActive()
                                && in_array($store->getWebsiteId(), $productWebsId)) {
                                $feedProduct = $this->helperGoogleFeed
                                                    ->getProductForFeed($product, $storeDetailForFeed);
                                $items = $this->helperGoogleFeed->insertFeedToGoogleShop($feedProduct);
                                $expireDate = $this->dateTime->gmtDate('Y-m-d H:i:s', strtotime("+30 days"));
                                $items = $this->helperGoogleFeed->insertFeedToGoogleShop($feedProduct);
                                if ($items['error'] == 0) {
                                    $syncsFeeds = [
                                        'feed_id' => $items['product']['id'],
                                        'mage_pro_id' => $product->getEntityId(),
                                        'store_id' => $storeDetailForFeed['store_id'],
                                        'expired_at' => $expireDate
                                    ];
                                    $mappedPro = $this->feedMap->create()->getCollection()
                                                        ->addFieldToFilter(
                                                            'mage_pro_id',
                                                            ['eq' => $requestData['product']]
                                                        )->addFieldToFilter(
                                                            'store_id',
                                                            ['eq' => $storeDetailForFeed['store_id']]
                                                        )->setPageSize(1)->getFirstItem();
                                    if ($mappedPro->getEntityId()) {
                                        $mappedPro->setExpiredAt($expireDate);
                                        $mappedPro->setFeedId($items['product']['id']);
                                    } else {
                                        $mappedPro->setData($syncsFeeds)->save();
                                    }
                                    $mappedPro->save();
                                    $result = ['error' => 0, 'total' => 1];
                                } else {
                                    /** $this->jsonHelperData->JsonDecode($items['message'])*/
                                    $errorMessage = json_decode($items['message']) != null ?
                                                    json_decode($items['message'], true) : $items['message'];
                                    $errorMessage = is_array($errorMessage) ?
                                        $errorMessage['error']['message'] : $errorMessage;
                                    $result = [
                                        'error' => 1,
                                        'total' => 1,
                                        'message' => $errorMessage.__(
                                            '; Store Product ID :- %1',
                                            $requestData['product']
                                        )
                                    ];
                                }
                            } else {
                                $result = [
                                    'error' => 1,
                                    'total' => 1,
                                    'message' => __(
                                        'Prouct is disabled/out of stock on store ;'
                                        .' Store ID :- %1 ; Product ID :- %2',
                                        $storeDetailForFeed['store_id'],
                                        $requestData['product']
                                    )
                                ];
                            }
                        }
                    }
                }
            }
            $this->getResponse()->representJson($this->jsonHelperData->jsonEncode($result));
        } catch (\Exception $e) {
            $result = ['error' => 1, 'message' => $e->getMessage()];
            $this->getResponse()->representJson($this->jsonHelperData->jsonEncode($result));
        }
    }

    /**
     * ValidateData
     *
     * @return array
     */
    private function validateData()
    {
        try {
            $accessToken = $this->helperGoogleFeed->helperData->getAccessToken();
            if ($this->getRequest()->isPost() && $accessToken) {
                $requestData = $this->getRequest()->getParams();
                if (!isset($requestData['finish']) /*&& !in_array($requestData['product'], $mappedPro)*/) {
                    $result =  ['requestData' => $requestData];
                } elseif (isset($requestData['finish'])) {
                    $total = (int) $requestData['count'] - (int) $requestData['skip'];
                    $msg = '<div class="wk-mu-success wk-mu-box">'
                                .__('Total %1 Product(s) Created.', $total)
                                .'</div>';
                    $msg .= '<div class="wk-mu-note wk-mu-box">'.__('Finished Execution.').'</div>';
                    $result = ['error' => 0, 'message' => $msg];
                }
            } else {
                $result = ['error' => 1, 'message' => __('Invalid request / Google feed account not authenticated.')];
            }
        } catch (\Exception $e) {
            $result = ['error' => 1, 'message' => $e->getMessage()];
        }
        return $result;
    }

    /**
     * Check product import permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_GoogleShoppingFeed::product_export');
    }
}
