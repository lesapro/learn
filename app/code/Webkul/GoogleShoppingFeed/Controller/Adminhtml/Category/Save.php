<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Controller\Adminhtml\Category;

use Webkul\GoogleShoppingFeed\Model\CategoryMapFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var Webkul\GoogleShoppingFeed\Model\CategoryMapFactory
     */
    private $categoryMap;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param CategoryMapFactory $categoryMap
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        CategoryMapFactory $categoryMap
    ) {
        $this->categoryMap = $categoryMap;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        try {
            $resultRedirect = $this->resultRedirectFactory->create();
            if ($this->getRequest()->isPost()) {
                $postData = $this->getRequest()->getParams();
                $mapedCategory = $this->categoryMap->create()->getCollection()
                                    ->addFieldToFilter('store_category_id', ['eq' => $postData['store_category_id']])
                                    ->addFieldToFilter(
                                        'google_feed_category',
                                        ['eq' => $postData['google_feed_category']]
                                    )->setPageSize(1)->getFirstItem();
                if (!$mapedCategory->getEntityId()) {
                    $postData = $this->getRequest()->getParams();
                    $categoryMapObj=$this->categoryMap->create();
                    $categoryMapObj->setData($postData);
                    $categoryMapObj->save();
                    $this->messageManager->addSuccess(__("Category mapped successfully."));
                } else {
                    $this->messageManager->addError(__("Category already mapped."));
                }
            } else {
                $this->messageManager->addError(__("Invalid request."));
            }
            return $resultRedirect->setPath('*/*/index');
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('*/*/index');
        }
    }

    /**
     * _isAllowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_GoogleShoppingFeed::maped_category_save');
    }
}
