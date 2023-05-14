<?php

namespace Fovoso\ImportProducts\Model;

use Magento\Catalog\Model\ResourceModel\Product\Gallery;

/**
 * Class ShippingFeeUpdater
 * @package Fovoso\ImportProducts\Model
 */
class ShippingFeeUpdater
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;
    private \Magento\Directory\Model\Config\Source\Country $country;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Directory\Model\Config\Source\Country $country
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->serializer = $serializer;
        $this->country = $country;
    }

    public function saveShipping($shippingFeeList, $logger)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('fovoso_shipping_detail');
        foreach ($shippingFeeList as $shippingFee) {

            $logger->info('Start import shipping for SKU:' . $shippingFee['sku']);
            $connection->insertOnDuplicate(
                $tableName,
                $shippingFee,
                ['sku', 'shipping_fee', 'shipping_meta']
            );
            $logger->info('save success for SKU:' . $shippingFee['sku']);
        }
    }

    public function getListShippingBySKU($sku, $country){
        $items = [];
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('fovoso_shipping_detail');
        $select = $connection->select()
            ->from($tableName)
            ->where('sku IN (?)', [$sku]);

        $list =  $connection->fetchAll($select);
        $result = false;
        $shippingCountry = [];
        foreach ($list as $item) {
            $shippingCountry = $this->serializer->unserialize($item['shipping_meta']);
        }

	    $shipFrom = '';
        foreach ($shippingCountry as $shipInfo) {
            if(strtolower($shipInfo['shipping_fee'])  == strtolower($country)) {
                $items[] =  $shipInfo;
            }
        }
        // if ($result) {
        //     echo '<pre>';
        //     var_dump($result);
        //     die();
        //     $time_shipping = $result['time_shipping'];
        //     usort($time_shipping, function($a, $b) {
        //         return $a['cost'] - $b['cost'];
        //     });
        //     return [
        //         'items' => $time_shipping,
        //         'location' => $country,
        //         'shipfrom' => $shipFrom
        //     ];
        // }
       
        return [
            'items' => $items,
            'location' => $country,
            'shipfrom' => $shipFrom
        ];
    }

    public function getShipFrom($sourcecode){
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('inventory_source');
        $select = $connection->select()
            ->from($tableName)
            ->where('source_code IN (?)', [$sourcecode]);

        $countryId =  $connection->fetchAll($select)[0]['country_id'];
        $list = $this->country->toOptionArray();
        $countryName = '';
        foreach ($list as $item) {
            if ($item['value'] == $countryId) {
                $countryName = $item['label'];
                break;
            }
        }
        return $countryName;
    }
}
