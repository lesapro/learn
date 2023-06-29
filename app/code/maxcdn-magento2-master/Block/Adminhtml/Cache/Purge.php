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
 * @license     http://creativecommons.org/licenses/bAy-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\MaxCDN\Block\Adminhtml\Cache;

use TIG\MaxCDN\Service\MaxCDNFactory;
use \Magento\Backend\Block\Template\Context;

class Purge extends \Magento\Backend\Block\Template
{

    /** @var MaxCDNFactory $maxCdnFactory */
    protected $maxCdnFactory;

    /**
     * Purge constructor.
     *
     * @param MaxCDNFactory $maxCdnFactory
     * @param Context $context
     */
    public function __construct(
        MaxCDNFactory $maxCdnFactory,
        Context $context
    ) {
        $this->maxCdnFactory = $maxCdnFactory;
        parent::__construct(
            $this->context = $context
        );
    }

    /**
     * @return string
     */
    public function getPurgeUrl()
    {
        return $this->getUrl('maxcdn/*/purge');
    }

    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface boolean
     */
    public function getIsEnabled()
    {
        return $this->maxCdnFactory->getIsEnabled();
    }
}
