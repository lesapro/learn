<?php

namespace Isobar\OneFieldName\Ui\Component\Listing\Address\Column;

use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Firstname
 * @package Isobar\OneFieldName\Ui\Component\Listing\Address\Column
 */
class Firstname extends Column
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * Firstname constructor.
     *
     * @param ContextInterface $context
     * @param DataHelper $dataHelper
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        DataHelper $dataHelper,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
    }

    /**
     * @inheritDoc
     */
    public function prepare()
    {
        $storeId = $this->dataHelper->getByKey(CustomerInterface::STORE_ID);
        $websiteId = $this->dataHelper->getByKey(CustomerInterface::WEBSITE_ID);
        if ($this->dataHelper->isShowOneFieldName($websiteId)
            && ($firstNameLabel = $this->dataHelper->getCustomerStoreLabel(AddressInterface::FIRSTNAME, $storeId))
        ) {
            $this->_data['config']['label'] = $firstNameLabel;
        }
        parent::prepare();
    }
}
