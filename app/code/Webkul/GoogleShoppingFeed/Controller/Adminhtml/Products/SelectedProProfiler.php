<?php
/**
 * GoogleShoppingFeed Admin Product Profiler Controller.
 * @package   Webkul_GoogleShoppingFeed
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Controller\Adminhtml\Products;

use Magento\Framework\Registry;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class SelectedProProfiler extends \Magento\Backend\App\Action
{
    /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var Magento\Ui\Component\MassAction\Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->filter = $filter;
        $this->coreRegistry = $registry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Save action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $productsIds = $collection->getColumnValues('entity_id');
            $this->coreRegistry->register('products_for_google_feed', $productsIds);
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Magento_Catalog::catalog_products');
            $resultPage->getConfig()->getTitle()->prepend(__('Product export to Google shopping feed'));
            return $resultPage;
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('catalog/product/index');
        }
    }

    /**
     * Check Product Profiler Permission Check.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_GoogleShoppingFeed::product_export');
    }
}
