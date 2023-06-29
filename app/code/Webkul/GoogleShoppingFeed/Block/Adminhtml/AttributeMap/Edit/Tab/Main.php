<?php

/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Block\Adminhtml\AttributeMap\Edit\Tab;

/**
 * Google shopping feed fields map form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Webkul\GoogleShoppingFeed\Model\Config\Source\AttributeList
     */
    private $attributeList;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Webkul\GoogleShoppingFeed\Model\Config\Source\AttributeList $attributeList
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Webkul\GoogleShoppingFeed\Model\Config\Source\AttributeList $attributeList,
        array $data = []
    ) {
        $this->attributeList = $attributeList->toOptionArray();
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    public function _prepareForm()
    {
        /** @var $model \Magento\User\Model\User */
        $data = $this->_coreRegistry->registry('google_field_map');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('feeds_fields_');

        $baseFieldset = $form->addFieldset('base_fieldset', ['legend' => __('Google Shopping Feeds')]);

        $baseFieldset->addField(
            'offerId',
            'select',
            [
                'name' => 'offerId',
                'label' => __('Product Id (offer Id)'),
                'id' => 'offerId',
                'title' => __('Unique identification of item'),
                'values' => $this->attributeList,
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'title',
            'select',
            [
                'name' => 'title',
                'label' => __('Title'),
                'id' => 'title',
                'title' => __('Name Of Product'),
                'values' => $this->attributeList,
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'description',
            'select',
            [
                'name' => 'description',
                'label' => __('Description'),
                'id' => 'description',
                'title' => __('Product Description'),
                'class' => 'required-entry',
                'values' => $this->attributeList,
                'required' => true
            ]
        );
        $baseFieldset->addField(
            'gtin',
            'select',
            [
                'name' => 'gtin',
                'label' => __('GTIN'),
                'id' => 'gtin',
                'title' => __('Product GTIN'),
                'class' => 'required-entry',
                'values' => $this->attributeList,
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'brand',
            'select',
            [
                'name' => 'brand',
                'label' => __('Brand'),
                'id' => 'brand',
                'title' => __('Brand'),
                'class' => 'required-entry',
                'values' => $this->attributeList,
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'color',
            'select',
            [
                'name' => 'color',
                'label' => __('Color'),
                'id' => 'color',
                'title' => __('Color'),
                'class' => 'required-entry',
                'values' => $this->attributeList,
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'mpn',
            'select',
            [
                'name' => 'mpn',
                'label' => __('MPN'),
                'id' => 'mpn',
                'title' => __('MPN'),
                'values' => $this->attributeList,
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'shippingWeight',
            'select',
            [
                'name' => 'shippingWeight',
                'label' => __('Shipping Weight'),
                'id' => 'weight',
                'title' => __('Shipping Weight'),
                'class' => 'required-entry',
                'values' => $this->attributeList,
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'imageLink',
            'select',
            [
                'name' => 'imageLink',
                'label' => __('Image Link'),
                'id' => 'imageLink',
                'title' => __('Image Link'),
                'class' => 'required-entry',
                'values' => $this->attributeList,
                'required' => true
            ]
        );
        $baseFieldset->addField(
            'sizes',
            'select',
            [
                'name' => 'sizes',
                'label' => __('Size'),
                'id' => 'sizes',
                'title' => __('Size'),
                'class' => 'required-entry',
                'values' => $this->attributeList,
                'required' => true
            ]
        );
        $baseFieldset->addField(
            'sizeSystem',
            'select',
            [
                'name' => 'sizeSystem',
                'label' => __('Size System'),
                'id' => 'sizes',
                'title' => __('Size System'),
                'values' => $this->attributeList
            ]
        );

        $baseFieldset->addField(
            'sizeType',
            'select',
            [
                'name' => 'sizeType',
                'label' => __('Size Type'),
                'id' => 'sizes',
                'title' => __('Size Type'),
                'values' => $this->attributeList
            ]
        );

        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
