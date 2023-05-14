<?php

namespace Isobar\OneFieldName\Ui\Component\Form\Field;

use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;

/**
 * Class Firstname
 * @package Isobar\OneFieldName\Ui\Component\Form\Field
 */
class Firstname extends Field
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
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();
        $storeId = $this->dataHelper->getByKey(CustomerInterface::STORE_ID);
        $websiteId = $this->dataHelper->getByKey(CustomerInterface::WEBSITE_ID);
        if ($this->dataHelper->isShowOneFieldName($websiteId)
            && ($firstNameLabel = $this->dataHelper->getCustomerStoreLabel(AddressInterface::FIRSTNAME, $storeId))
        ) {
            $config = $this->getData('config');
            $config['label'] = $firstNameLabel;
            $this->setData('config', $config);
        }
    }
}
