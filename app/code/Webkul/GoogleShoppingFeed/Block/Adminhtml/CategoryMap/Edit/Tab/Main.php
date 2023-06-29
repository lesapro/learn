<?php

/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Block\Adminhtml\CategoryMap\Edit\Tab;

/**
 * Google shopping feed fields map form main tab
 *
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
     * @param \Webkul\GoogleShoppingFeed\Model\Config\Source\CategoriesList $categoryList
     * @param \Webkul\GoogleShoppingFeed\Model\Config\Source\GoogleFeedCategoriesList $googleFeedCategoriesList
     * @param array $data = []
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Webkul\GoogleShoppingFeed\Model\Config\Source\CategoriesList $categoryList,
        \Webkul\GoogleShoppingFeed\Model\Config\Source\GoogleFeedCategoriesList $googleFeedCategoriesList,
        array $data = []
    ) {
        $this->categoryList = $categoryList->toOptionArray();
        $this->googleFeedCategoriesList = $googleFeedCategoriesList->toOptionArray();
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

        $baseFieldset = $form->addFieldset('base_fieldset', ['legend' => __('Google Shopping Category Map')]);

        $baseFieldset->addField(
            'store_category_id_tmp',
            'select',
            [
                'name' => 'store_category_id_tmp',
                'label' => __('Store Category'),
                'id' => 'store_category_id',
                'title' => __('Magento Store Category'),
                'values' => $this->categoryList,
                'class' => 'mage_category',
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'google_feed_category_tmp',
            'select',
            [
                'name' => 'google_feed_category_tmp',
                'label' => __('Google Feed Category'),
                'id' => 'google_feed_category_tmp',
                'title' => __('Google Feed Category'),
                'class' => 'google_feed_category',
                'values' => $this->googleFeedCategoriesList,
                'required' => true
            ]
        );
        $baseFieldset->addField(
            'google_feed_category',
            'hidden',
            [
                'name' => 'google_feed_category',
                'label' => __('Google Feed Category'),
                'id' => 'google_feed_category',
                'title' => __('Google Feed Category'),
            ]
        );

        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
