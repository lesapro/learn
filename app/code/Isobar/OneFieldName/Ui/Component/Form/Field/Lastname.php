<?php

namespace Isobar\OneFieldName\Ui\Component\Form\Field;

use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;

/**
 * Class Lastname
 * @package Isobar\OneFieldName\Ui\Component\Form\Field
 */
class Lastname extends Field
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

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
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();
        $websiteId = $this->dataHelper->getByKey(CustomerInterface::WEBSITE_ID);
        if ($websiteId && $this->dataHelper->isShowOneFieldName($websiteId)) {
            $config = $this->getData('config');
            $config['visible'] = false;
            $config['validation']['required-entry'] = false;
            $this->setData('config', $config);
        }
    }
}
