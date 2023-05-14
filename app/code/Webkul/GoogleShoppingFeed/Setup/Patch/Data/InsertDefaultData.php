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

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InsertDefaultData implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var \Magento\Framework\File\Csv
     */
    private $csv;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Framework\File\Csv $csv
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Framework\File\Csv $csv
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->csv = $csv;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $connection = $this->moduleDataSetup->getConnection();
        $csvData = $this->csv->getData(__DIR__.'/google_feed_category.csv');
        $categoryList = [];
        foreach ($csvData as $row) {
            $categoryList[] = [
                'entity_id' => $row[0],
                'level0' => $row[1],
                'level1' => $row[2],
                'level2' => $row[3],
                'level3' => $row[4],
                'level4' => $row[5],
                'level5' => $row[6],
                'level6' => $row[7]
            ];
        }

        $connection->insertMultiple($this->moduleDataSetup->getTable('google_feed_category'), $categoryList);
        $this->moduleDataSetup->getConnection()->endSetup();
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

    /**
     * Get Dependencies
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }
}
