<?php
/**
 * Webkul GoogleShoppingFeed Data Helper
 * @category  Webkul
 * @package   Webkul_GoogleShoppingFeed
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Controller\Adminhtml\Products;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\GoogleShoppingFeed\Model\ResourceModel\GoogleFeedMap\CollectionFactory;
use Webkul\GoogleShoppingFeed\Helper\GoogleFeed;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter.
     *
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var GoogleFeed
     */
    private $helperGoogleFeed;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param GoogleFeed $helperGoogleFeed
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        GoogleFeed $helperGoogleFeed
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->helperGoogleFeed = $helperGoogleFeed;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $orderRecordDeleted = 0;
            $accessToken = $this->helperGoogleFeed->helperData->getAccessToken();
            foreach ($collection->getItems() as $feedMap) {
                try {
                    if ($accessToken) {
                        $this->helperGoogleFeed->deleteFeedFromGoogleShop($feedMap->getFeedId());
                    }
                    $feedMap->setId($feedMap->getEntityId());
                    $this->deleteMappedOrderRecord($feedMap);
                    ++$orderRecordDeleted;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    continue;
                }
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $orderRecordDeleted));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
        }
    }

    /**
     * Delete MappedOrderRecord
     *
     * @param Webkul\GoogleShoppingFeed\Model\OrderMap $feedMap
     * @return void
     */
    private function deleteMappedOrderRecord($feedMap)
    {
        $feedMap->delete();
    }

    /**
     * Check Order Map recode delete Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_GoogleShoppingFeed::feed_map_delete');
    }
}
