<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Model\Config\Source;

use Magento\Framework\Exception\LocalizedException;

class CategoriesList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(\Magento\Catalog\Model\CategoryFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Return options array.
     *
     * @param int $store
     *
     * @return array
     */
    public function toOptionArray($store = null)
    {
        try {
            $categoriesArr = [['value' => '', 'label'=> __('----- Select ------')]];
            $categories = $this->categoryFactory->create()->getCollection();
            foreach ($categories as $category) {
                $category = $this->getCategory($category->getEntityId());
                if ($category->getName() === 'Root Catalog') {
                    continue;
                }
                $categoriesArr[] = ['value' => $category->getEntityId(),'label' => $category->getName()];
            }

            return $categoriesArr;
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * Get options in "key-value" format.
     *
     * @return array
     */
    public function toArray()
    {
        $optionList = $this->toOptionArray();
        $optionArray = [];
        foreach ($optionList as $option) {
            $optionArray[$option['value']] = $option['label'];
        }
        return $optionArray;
    }

    /**
     * Get Category
     *
     * @param int $categoryId
     * @return Magento/Catalog/Model/Category
     */
    private function getCategory($categoryId)
    {
        return $this->categoryFactory->create()->load($categoryId);
    }
}
