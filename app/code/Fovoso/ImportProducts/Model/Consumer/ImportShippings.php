<?php

namespace Fovoso\ImportProducts\Model\Consumer;

use Fovoso\ImportProducts\Logger\Logger;

class ImportShippings
{

    /**
     * @var Logger
     */
    private $logger;


    /**
     * @var Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    public function __construct(
        Logger $logger,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->resourceConnection = $resourceConnection;
    }

    public function process($data)
    {
        $this->logger->info('start import shipping');
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName('fovoso_shipping_detail');
            $rows = [];
            foreach ($data as $shipping) {
                if (gettype($shipping) == "string") {
                    $shipping = $this->serializer->unserialize($shipping);
                }
                $rows[] = [
                    'sku' => $shipping['sku'],
                    'shipping_meta' => $this->serializer->serialize($shipping['shipping_meta']),
                    'shipping_fee' => $shipping['shipping_fee'],
                ];
            }
            $result = $connection->insertMultiple($tableName, $rows);
            $this->logger->info('imported ' . $result . ' shipping');
        } catch (\Exception $e) {
            $this->logger->info('imported shipping error: ' . $e->getMessage());
        }
    }
}
