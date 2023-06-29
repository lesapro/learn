<?php

namespace Fovoso\ImportProducts\Model;

use Fovoso\ImportProducts\Logger\Logger;
use Magento\Framework\ObjectManagerInterface;

class ImportConsumer
{

    protected $consumerHandles = [
        'importProducts' => '\Fovoso\ImportProducts\Model\Consumer\ImportProducts',
        'importShippings' => '\Fovoso\ImportProducts\Model\Consumer\ImportShippings',
    ];

    /**
     * @var ObjectManagerInterface
     */
    private $objectmanager;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        ObjectManagerInterface $objectmanager,
        Logger $logger
    ) {
        $this->objectmanager = $objectmanager;
        $this->logger = $logger;
    }


    public function processMessage(\Fovoso\ImportProducts\Api\RbMessageInterface $message)
    {
        $this->logger->info('==========START IMPORT==========');
        $type = $message->getType();
        $handleObject =  $this->consumerHandles[$type] ?? null;
        if ($handleObject) {
            $this->objectmanager->create($handleObject)->process($message->getMessage());
        }
        $this->logger->info('==========END IMPORT==========');
    }
}
