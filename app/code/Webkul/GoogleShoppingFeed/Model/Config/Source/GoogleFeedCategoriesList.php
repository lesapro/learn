<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Model\Config\Source;

class GoogleFeedCategoriesList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param \Webkul\GoogleShoppingFeed\Model\GoogleFeedCategoryFactory $categoryFactory
     */
    public function __construct(\Webkul\GoogleShoppingFeed\Model\GoogleFeedCategoryFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Return options array.
     *
     * @param int $store
     * @return array
     */
    public function toOptionArray($store = null)
    {
        $categoriesArr = [['value' => '','label' => __('------Select------')]];
        $categories = $this->categoryFactory->create()->getCollection()->getColumnValues('level0');
        $categories = array_unique($categories);
        foreach ($categories as $category) {
            $categoriesArr[] = ['value' => $category,'label' => $category];
        }

        return $categoriesArr;
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
}
