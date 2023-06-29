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
use Webkul\GoogleShoppingFeed\Model\GoogleFeedCategoryFactory;

class GoogleFeedChildCategory extends \Magento\Backend\App\Action
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
     * @var Webkul\GoogleShoppingFeed\Model\GoogleFeedCategoryFactory;
     */
    private $categoryFactory;

    /**
     * Class constructor
     * @param \Magento\Backend\App\Action\Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CategoryFactory $categoryFactory
     * @return void
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory,
        GoogleFeedCategoryFactory $categoryFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Execute@var
     */
    public function execute()
    {
        try {
            $postData = $this->getRequest()->getParams();
            $resultJson = $this->resultJsonFactory->create();
            if (isset($postData['cat_id'])) {
                $level = isset($postData['level']) ? $postData['level'] : 0;
                $valLevel = $level+1;
                $childcategory = $this->categoryFactory->create()->getCollection()
                                            ->addFieldToFilter('level'.$level, ['like' => $postData['cat_id']])
                                            ->getColumnValues('level'.$valLevel);
                $childcategory = array_unique($childcategory);
                $categoryList = [];
                $categoryList[] = ['value' => '', 'label' => __('------Select------')];
                $total = 0;
                foreach ($childcategory as $category) {
                    if ($category) {
                        $categoryList[] = ['value' => $category, 'lable' => $category];
                        $total++;
                    }
                }
                $level++;

                $response = ['items' => $categoryList, 'totalRecords' => $total, 'level' => $level];
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
