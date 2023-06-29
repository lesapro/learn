<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Model\Config\Source;

class AttributeList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $productAttribute;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteria;

    /**
     * @var \Webkul\GoogleShoppingFeed\Logger\Logger
     */
    private $logger;

    /**
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria
     * @param \Magento\Framework\Api\SortOrder $sortOrder
     * @param \Webkul\GoogleShoppingFeed\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria,
        \Magento\Framework\Api\SortOrder $sortOrder,
        \Webkul\GoogleShoppingFeed\Logger\Logger $logger
    ) {
        $this->productAttribute = $productAttributeRepository;
        $this->sortOrder = $sortOrder;
        $this->searchCriteria = $searchCriteria;
        $this->logger = $logger;
    }
    /**
     * Return options array.
     *
     * @param int $store
     * @return array
     */
    public function toOptionArray($store = null)
    {
        try {
            $optionArray = [
                ['label' => __('-----Select-----'), 'value' => ''],
                ['label' => __('Create New Attribute'), 'value' => 'new']
            ];
            $sortOrder = $this->sortOrder;
            $sortOrder->setField('frontend_label');
            $sortOrder->setDirection('ASC');
            $searchCriteria = $this->searchCriteria->addFilter(
                'frontend_input',
                ['swatch_text', 'swatch_visual'],
                'nin'
            )->addFilter('frontend_label', '', 'neq')->setSortOrders([$sortOrder])->create();
            $attributeList = $this->productAttribute->getList($searchCriteria)->getItems();
            foreach ($attributeList as $attr) {
                $label = $attr->getFrontendLabel().' ('.$attr->getAttributeCode().')';
                $optionArray[] = ['label' => $label , 'value' => $attr->getAttributeCode()];
            }
        } catch (\Exception $e) {
            $this->logger->addError('getProductAttributeList : '. $e->getMessage());
            $optionArray = [];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->toOptionArray();
    }
}
