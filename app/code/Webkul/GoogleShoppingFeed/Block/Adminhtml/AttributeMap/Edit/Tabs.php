<?php

/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Block\Adminhtml\AttributeMap\Edit;

/**
 * Warehouse page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Google Shopping Feed Information'));
    }

    /**
     * _beforeToHtml
     *
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->addTab(
            'main_section',
            [
                'label' => __('Google Shopping Feed Info'),
                'title' => __('Google Shopping Feed Info'),
                'content' => $this->getLayout()
                    ->createBlock(\Webkul\GoogleShoppingFeed\Block\Adminhtml\AttributeMap\Edit\Tab\Main::class)
                    ->toHtml(),
                'active' => true
            ]
        );
        return parent::_beforeToHtml();
    }
}
