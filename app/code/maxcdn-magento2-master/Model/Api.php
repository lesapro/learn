<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\MaxCDN\Model;

use TIG\MaxCDN\Service\MaxCDNFactory;
use Magento\Framework\Message\ManagerInterface;

class Api {

    /** @var ManagerInterface $messageManager */
    private $messageManager;

    /** @var MaxCDNFactory $maxCdnFactory */
    public $maxCdnFactory;

    /**
     * Api constructor.
     *
     * @param MaxCDNFactory $maxCdnFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        MaxCDNFactory $maxCdnFactory,
        ManagerInterface $messageManager
    )
    {
        $this->maxCdnFactory = $maxCdnFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * Generates MaxCDN class from composer library.
     *
     * @return \MaxCDN
     */
    public function getConnection() {
        return $this->maxCdnFactory->create();
    }

    /**
     * @return ManagerInterface $messageManager
     */
    public function getMessageManager() {
        return $this->messageManager;
    }
}
