<?php
/**
 * GoogleShoppingFeed Admin Product Profiler Block.
 * @category  Webkul
 * @package   Webkul_GoogleShoppingFeed
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Block\Adminhtml\FeedMap;

use Magento\Framework\Exception\LocalizedException;

class SelectedProProfiler extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->coreRegistry = $registry;
    }

    /**
     * For get total imported product count.
     *
     * @return int
     */
    public function getImportedProduct()
    {
        try {
            /** @var $model \Magento\User\Model\User */
            $productList = $this->coreRegistry->registry('products_for_google_feed');
            return $productList;
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * Get CreateProUrl
     *
     * @return string
     */
    public function getCreateProUrl()
    {
        return $this->getUrl('googleshoppingfeed/products/export');
    }
}
