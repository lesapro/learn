<?php

namespace Fovoso\Shipping\Block;

use Magento\Framework\View\Element\Template;
use Fovoso\Shipping\Model\ResourceModel\ShippingFree\CollectionFactory;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

/**
 * Class ShippingPolicy
 * @package Fovoso\Shipping\Block
 */
class ShippingPolicy extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * ShippingPolicy constructor.
     * @param Template\Context $context
     * @param CollectionFactory $collectionFactory
     * @param PriceHelper $priceHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        PriceHelper $priceHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->priceHelper = $priceHelper;
    }

    /**
     * @return $this|ShippingPolicy
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Shipping Policy'));
        if ($this->getCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'custom.history.pager',
                ['data' => ['path' => 'fovoso_shipping/shipping/policy']]
            )->setAvailableLimit([10 => 10, 15 => 15, 20 => 20])
            ->setShowPerPage(false)->setCollection(
                $this->getCollection()
            );
            $this->setChild('pager', $pager);
            $this->getCollection()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return \Fovoso\Shipping\Model\ResourceModel\ShippingFree\Collection
     */
    public function getCollection()
    {
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;
        $collection = $this->collectionFactory->create();
        // Filter
        if ($this->getData('country')) {
            $collection->addFieldToFilter('country', $this->getData('country'));
        }
        if ($keyword = $this->getData('keyword')) {
            $collection->getSelect()
                ->where(new \Zend_Db_Expr("country LIKE '%{$keyword}%'"))
                ->orWhere(new \Zend_Db_Expr("shipping_method LIKE '%{$keyword}%'"))
                ->orWhere(new \Zend_Db_Expr("shipping_time LIKE '%{$keyword}%'"))
                ->orWhere(new \Zend_Db_Expr("costs LIKE '%{$keyword}%'"));
        }
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    /**
     * @param $price
     * @return float|string
     */
    public function getFormattedPrice($price)
    {
        return $this->priceHelper->currency(number_format($price, 2), true, false);
    }

    /**
     * @return \Fovoso\Shipping\Model\ResourceModel\ShippingFree\Collection
     */
    public function getCountry()
    {
        return $this->collectionFactory->create()
            ->addFieldToSelect('country');
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->_urlBuilder->getUrl('fovoso_shipping/shipping/request');
    }
}
