<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Controller\Adminhtml\Category;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $catRecordDeleted = 0;
        foreach ($collection->getItems() as $categoryMap) {
            $categoryMap->setId($categoryMap->getEntityId());
            $this->deleteObject($categoryMap);
            ++$catRecordDeleted;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $catRecordDeleted));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    /**
     * DeleteObject
     *
     * @param Object $object
     * @return void
     */
    private function deleteObject($object)
    {
        $object->delete();
    }

    /**
     * Check Category Map recode delete Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_GoogleShoppingFeed::cat_map_delete');
    }
}
