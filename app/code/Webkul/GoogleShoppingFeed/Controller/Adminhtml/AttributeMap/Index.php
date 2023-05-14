<?php

/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Controller\Adminhtml\AttributeMap;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Webkul\GoogleShoppingFeed\Model\AttributeMapFactory $attributeMap
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Webkul\GoogleShoppingFeed\Model\AttributeMapFactory $attributeMap
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->attributeMap = $attributeMap;
    }

    /**
     * Mapped Categories List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $mappedRecords = $this->attributeMap->create()->getCollection();
        $mappedData = [];
        foreach ($mappedRecords as $record) {
            $mappedData[$record->getGoogleFeedField()] = $record->getAttributeCode();
        }
        $mappedData['offerId'] = isset($mappedData['offerId']) ? $mappedData['offerId'] : 'sku';
        $mappedData['title'] = isset($mappedData['title']) ? $mappedData['title'] : 'name';
        $mappedData['description'] = isset($mappedData['description']) ? $mappedData['description'] : 'description';
        $mappedData['shippingWeight'] = isset($mappedData['shippingWeight']) ? $mappedData['shippingWeight'] : 'weight';
        $mappedData['brand'] = isset($mappedData['brand']) ? $mappedData['brand'] : 'manufacturer';
        $mappedData['color'] = isset($mappedData['color']) ? $mappedData['color'] : 'color';
        $mappedData['imageLink'] = isset($mappedData['imageLink']) ? $mappedData['imageLink'] : 'thumbnail';

        $this->coreRegistry->register('google_field_map', $mappedData);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_GoogleShoppingFeed::maped_attribute');
        $resultPage->getConfig()->getTitle()->prepend(__('Map Google Shopping'));
        return $resultPage;
    }

    /**
     * _isAllowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_GoogleShoppingFeed::maped_attribute');
    }
}
