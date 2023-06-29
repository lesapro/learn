<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Model;

use Webkul\GoogleShoppingFeed\Helper\Data as GoogleFeedHelperData;
use Webkul\GoogleShoppingFeed\Logger\Logger;

/**
 * custom cron actions
 */
class Cron
{
    /**
     * @var \Webkul\GoogleShoppingFeed\Helper\Data
     */
    private $googleFeedHelperData;

    /**
     * @var \Webkul\GoogleShoppingFeed\Logger\Logger
     */
    private $logger;

    /**
     * Class constructor
     * @param GoogleFeedHelperData $googleFeedHelperData
     * @param Logger $logger
     */
    public function __construct(
        GoogleFeedHelperData $googleFeedHelperData,
        Logger $logger
    ) {
        $this->googleFeedHelperData = $googleFeedHelperData;
        $this->logger = $logger;
    }

    /**
     * AccessTokenValidate
     *
     * @return void
     */
    public function accessTokenValidate()
    {
        $this->logger->info("===============GoogleFeed access Token Cron execution start ================ ");
        try {
            $this->googleFeedHelperData->refreshAccessToken();
        } catch (\Exception $e) {
            $this->logger->addError('GoogleFeed access Token Validate :'.$e->getMessage());
        }
    }
}
