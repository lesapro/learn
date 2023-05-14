<?php

namespace Isobar\OneFieldName\Block\Adminhtml\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class Name
 * @package Isobar\OneFieldName\Block\Adminhtml\Grid\Renderer
 */
class Name extends AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        return $row->getName();
    }
}
