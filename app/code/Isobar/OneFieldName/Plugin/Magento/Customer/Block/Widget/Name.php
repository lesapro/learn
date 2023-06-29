<?php

namespace Isobar\OneFieldName\Plugin\Magento\Customer\Block\Widget;

use Isobar\OneFieldName\Helper\Data as DataHelper;

/**
 * Class Name
 * @package Isobar\OneFieldName\Plugin\Magento\Customer\Block\Widget
 */
class Name
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * Name constructor.
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Customer\Block\Widget\Name $block
     *
     * @return array
     */
    public function beforeToHtml(\Magento\Customer\Block\Widget\Name $block)
    {
        if ($this->dataHelper->isShowOneFieldName()) {
            $block->setTemplate('Isobar_OneFieldName::widget/name.phtml');
        }
        return [];
    }
}
