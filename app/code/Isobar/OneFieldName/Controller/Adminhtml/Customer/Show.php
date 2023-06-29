<?php

namespace Isobar\OneFieldName\Controller\Adminhtml\Customer;

use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Store\Api\StoreWebsiteRelationInterface;
use Magento\Store\Model\Store;

/**
 * Class Show
 * @package Isobar\OneFieldName\Controller\Adminhtml\Customer
 */
class Show extends Action
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var ResultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var StoreWebsiteRelationInterface
     */
    protected $storeWebsiteRelation;

    /**
     * Show constructor.
     *
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param ResultJsonFactory $resultJsonFactory
     * @param StoreWebsiteRelationInterface $storeWebsiteRelation
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        ResultJsonFactory $resultJsonFactory,
        StoreWebsiteRelationInterface $storeWebsiteRelation
    ) {
        $this->dataHelper = $dataHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeWebsiteRelation = $storeWebsiteRelation;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $websiteId = $this->getRequest()->getParam('websiteId');
        $storeId = $this->getStoreIdByWebsiteId($websiteId);

        $isShow = $this->dataHelper->isShowOneFieldName($websiteId);
        $firstNameLabel = $this->dataHelper->getCustomerStoreLabel(CustomerInterface::FIRSTNAME, $storeId);

        /** @var ResultJson $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData([
            'is_show' => $isShow,
            'label' => $firstNameLabel ?: ''
        ]);
    }

    /**
     * Get store id by website id
     *
     * @param int $websiteId
     *
     * @return int
     */
    private function getStoreIdByWebsiteId($websiteId)
    {
        $storeIds = $this->storeWebsiteRelation->getStoreByWebsiteId($websiteId);
        return !empty($storeIds) ? array_shift($storeIds) : Store::DEFAULT_STORE_ID;
    }
}
