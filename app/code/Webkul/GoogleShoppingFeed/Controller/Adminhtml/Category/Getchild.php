<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Controller\Adminhtml\Category;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\CategoryFactory;

class Getchild extends \Magento\Backend\App\Action
{
    /**
     * @var Magento\Framework\Controller\Result\JsonFactory;
     */
    private $resultJsonFactory;

    /**
     * @var Webkul\GoogleShoppingFeed\Helper\EtsyDetail
     */
    private $etsyDetail;

    /**
     * @var Magento\Catalog\Model\CategoryFactory;
     */
    private $categoryFactory;

    /**
     * Class constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CategoryFactory $categoryFactory
     * @return void
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory,
        CategoryFactory $categoryFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Execute
     */
    public function execute()
    {
        try {
            $postData = $this->getRequest()->getParams();
            $resultJson = $this->resultJsonFactory->create();
            if (isset($postData['cat_id'])) {
                $childcategory = $this->categoryFactory->create()->getCollection()
                                            ->addFieldToFilter('parent_id', ['eq' => $postData['cat_id']])->load();

                $categoryList = [];
                foreach ($childcategory as $category) {
                    $category = $this->getCategory($category->getEntityId());
                    $categoryList[] = ['value' => $category->getEntityId(), 'lable' => $category->getName()];
                }
                $response = ['items' => $categoryList, 'totalRecords' => count($categoryList)];
            } else {
                $response = ['error' => true, 'msg'=> __('Invalid request')];
            }
        } catch (\Exception $e) {
            $response = ['error' => true, 'msg'=> __($e->getMessage())];
        }
        return $resultJson->setData($response);
    }

    /**
     * Get Category
     *
     * @param int $categoryId
     * @return \Magento\Catalog\Model\Category $category
     */

    private function getCategory($categoryId)
    {
        return $this->categoryFactory->create()->load($categoryId);
    }
}
