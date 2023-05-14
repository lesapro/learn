<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Block\Adminhtml\AttributeMap;

/**
 * User edit page
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_controller = 'adminhtml_attributeMap';
        $this->_blockGroup = 'Webkul_GoogleShoppingFeed';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Mapping Data'));
        $this->buttonList->remove('back');
    }

    /**
     * Get HeaderText
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Google Shopping Feed Fields');
    }
}
