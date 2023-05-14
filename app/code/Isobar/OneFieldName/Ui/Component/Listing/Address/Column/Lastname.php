<?php

namespace Isobar\OneFieldName\Ui\Component\Listing\Address\Column;

use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Lastname
 * @package Isobar\OneFieldName\Ui\Component\Listing\Address\Column
 */
class Lastname extends Column
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * Lastname constructor.
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
        $websiteId = $this->dataHelper->getByKey(CustomerInterface::WEBSITE_ID);
        if ($this->dataHelper->isShowOneFieldName($websiteId)) {
            $this->_data['config']['componentDisabled'] = true;
        }
        parent::prepare();
    }
}
