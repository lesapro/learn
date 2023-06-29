<?php

namespace Isobar\OneFieldName\Setup\Patch\Data;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class UpdateCustomerName
 * @package Isobar\OneFieldName\Setup\Patch\Data
 */
class UpdateCustomerName implements DataPatchInterface
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * UpdateCustomerName constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->storeManager = $storeManager;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('customer_eav_attribute_website');

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerLastName = $eavSetup->getAttribute(Customer::ENTITY, CustomerInterface::LASTNAME);
        $addressLastName = $eavSetup->getAttribute('customer_address', AddressInterface::LASTNAME);

        $websites = $this->storeManager->getWebsites();
        foreach ($websites as $website) {
            $connection->insertOnDuplicate(
                $tableName,
                [
                    'attribute_id' => $customerLastName['attribute_id'],
                    'website_id' => $website->getId(),
                    'is_visible' => $customerLastName['is_visible'],
                    'is_required' => $customerLastName['is_required']
                ],
                ['attribute_id', 'website_id', 'is_visible', 'is_required']
            );

            $connection->insertOnDuplicate(
                $tableName,
                [
                    'attribute_id' => $addressLastName['attribute_id'],
                    'website_id' => $website->getId(),
                    'is_visible' => $addressLastName['is_visible'],
                    'is_required' => $addressLastName['is_required']
                ],
                ['attribute_id', 'website_id', 'is_visible', 'is_required']
            );
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
