<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleShoppingFeed
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class CreateAttributes implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Apply
     *
     * @return void
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gf_age_group',
            [
                'type' => 'varchar',
                'label' => 'Age Group',
                'input' => 'select',
                'group' => 'Google Shopping Feed Fields',
                'source' => \Webkul\GoogleShoppingFeed\Model\Config\Source\AgeGroupList::class,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'visible_on_front' => false,
                'is_configurable' => false,
                'searchable' => false,
                'note' => 'Target age group of the item.',
                'default' => '',
                'filterable' => true,
                'comparable' => true,
                'visible_in_advanced_search' => false,
                'apply_to' => 'simple,downloadable,virtual,bundle,configurable',
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gf_for_gender',
            [
                'type' => 'varchar',
                'label' => 'Product avilable for',
                'input' => 'select',
                'group' => 'Google Shopping Feed Fields',
                'source' => \Webkul\GoogleShoppingFeed\Model\Config\Source\GenderOptions::class,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'visible_on_front' => false,
                'is_configurable' => false,
                'searchable' => false,
                'note' => 'Target gender of the item.',
                'default' => '',
                'filterable' => true,
                'comparable' => true,
                'visible_in_advanced_search' => false,
                'apply_to' => 'simple,downloadable,virtual,bundle,configurable',
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gf_condition',
            [
                'type' => 'varchar',
                'label' => 'Product Condition',
                'input' => 'select',
                'group' => 'Google Shopping Feed Fields',
                'source' => \Webkul\GoogleShoppingFeed\Model\Config\Source\ConditionOptions::class,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'visible_on_front' => false,
                'is_configurable' => false,
                'searchable' => false,
                'default' => '',
                'note' => 'Condition or state of the item.',
                'filterable' => true,
                'comparable' => true,
                'visible_in_advanced_search' => false,
                'apply_to' => 'simple,downloadable,virtual,bundle,configurable',
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gf_tax_rate',
            [
                'type' => 'int',
                'label' => 'Tax Rate',
                'input' => 'text',
                'group' => 'Google Shopping Feed Fields',
                'source' => '',
                'frontend_class' => 'validate-greater-than-zero',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'visible_on_front' => false,
                'is_configurable' => false,
                'searchable' => false,
                'default' => '',
                'note' => 'Tax Rate percentage (%) of the item according to store country.',
                'filterable' => true,
                'comparable' => true,
                'visible_in_advanced_search' => false,
                'apply_to' => 'simple,downloadable,virtual,bundle,configurable',
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gf_tax_on_ship',
            [
                'type' => 'int',
                'label' => 'Tax Rate Apply On Ship',
                'input' => 'boolean',
                'group' => 'Google Shopping Feed Fields',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'frontend_class' => 'validate-greater-than-zero',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'visible_on_front' => false,
                'is_configurable' => false,
                'searchable' => false,
                'default' => '',
                'filterable' => true,
                'comparable' => true,
                'visible_in_advanced_search' => false,
                'apply_to' => 'simple,downloadable,virtual,bundle,configurable',
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gf_custom_product',
            [
                'type' => 'int',
                'label' => 'Custom Product',
                'input' => 'boolean',
                'group' => 'Google Shopping Feed Fields',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'frontend_class' => 'validate-greater-than-zero',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'visible_on_front' => false,
                'is_configurable' => false,
                'searchable' => false,
                'default' => '',
                'filterable' => true,
                'comparable' => true,
                'visible_in_advanced_search' => false,
                'apply_to' => 'simple,downloadable,virtual,bundle,configurable',
            ]
        );
    }

    /**
     * Get Dependencies
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get Aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
