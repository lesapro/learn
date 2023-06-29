<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed uninstall
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface
{
    /**
     * __construct
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Uninstall
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'gf_age_group');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'gf_for_gender');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'gf_condition');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'gf_tax_rate');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'gf_tax_on_ship');
        $setup->endSetup();
    }
}
