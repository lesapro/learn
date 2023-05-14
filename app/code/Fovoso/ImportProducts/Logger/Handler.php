<?php

namespace Fovoso\ImportProducts\Logger;

use Magento\Framework\Logger\Handler\Base;

/**
 * Class Handler
 * @package Fovoso\ImportProducts\Logger
 */
class Handler extends Base
{
    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/fovoso/product_import.log';

    /**
     * Logging level
     * @var int
     */
    protected $loggerType = \Monolog\Logger::DEBUG;
}
