<?php

namespace Fovoso\Shipping\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class ImportFovosoShipping
 * @package Fovoso\Shipping\Setup\Patch\Data
 */
class ImportFovosoShipping implements DataPatchInterface
{
    /**
     * ModuleDataSetupInterface
     *
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * @var ReadFactory
     */
    protected $readFactory;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * ImportFovosoShipping constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ComponentRegistrar $componentRegistrar
     * @param ReadFactory $readFactory
     * @param Json $serializer
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ComponentRegistrar $componentRegistrar,
        ReadFactory $readFactory,
        Json $serializer
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->componentRegistrar = $componentRegistrar;
        $this->readFactory = $readFactory;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $moduleDir = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Fovoso_Shipping');
        $directoryRead = $this->readFactory->create($moduleDir);
        $fileAbsolutePath = $moduleDir . '/view/adminhtml/web/shipping/shipping.json';
        $filePath = $directoryRead->getRelativePath($fileAbsolutePath);
        $shipping = $this->serializer->unserialize($directoryRead->readFile($filePath));
        $data = [];
        foreach ($shipping['original_rows'] as $row) {
            $row['value']['costs'] = (int) str_replace('$', '', $row['value']['costs']);
            $data[] = $row['value'];
        }
        $this->moduleDataSetup->getConnection()
            ->insertMultiple(
                $this->moduleDataSetup->getTable('fovoso_shipping'),
                $data
            );
        $this->moduleDataSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
