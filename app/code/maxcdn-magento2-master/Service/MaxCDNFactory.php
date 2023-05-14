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

namespace TIG\MaxCDN\Service;

use MaxCDN;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManager\ObjectManager;

/**
 * To auto generate the MaxCDN class we are using the ObjectManager.
 */
class MaxCDNFactory
{
    /** @var ObjectManager $objectManager */
    private $objectManager;

    /** @var ScopeConfigInterface $scopeConfig */
    private $scopeConfig;

    const TIG_MAXCDN_IS_ENABLED = 'tig_max_cdn/general/cdn_is_enabled';
    const TIG_MAXCDN_COMPANY_ALIAS = 'tig_max_cdn/general/cdn_company_alias';
    const TIG_MAXCDN_CONSUMER_KEY = 'tig_max_cdn/general/cdn_consumer_key';
    const TIG_MAXCDN_CONSUMER_SECRET = 'tig_max_cdn/general/cdn_consumer_secret';

    /**
     * MaxCDNFactory constructor.
     *
     * @param MaxCDN $maxCdn
     * @param ObjectManager $objectManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ObjectManager $objectManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return MaxCDN
     */
    public function create() {
        $alias = $this->getConfigValue(static::TIG_MAXCDN_COMPANY_ALIAS);
        $key = $this->getConfigValue(static::TIG_MAXCDN_CONSUMER_KEY);
        $secret = $this->getConfigValue(static::TIG_MAXCDN_CONSUMER_SECRET);

        return $this->objectManager->create(MaxCDN::class, [
            'alias'  => $alias,
            'key'    => $key,
            'secret' => $secret
        ]);
    }

    /**
     * @return ScopeConfigInterface
     */
    public function getIsEnabled() {
        return $this->getConfigValue(static::TIG_MAXCDN_IS_ENABLED);
    }

    /**
     * @param $group string
     * @param $field string
     * @return ScopeConfigInterface
     */
    public function getConfigValue($field) {
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $configValue = $this->getScopeConfig()->getValue($field, $scope);

        return $configValue;
    }

    /**
     * @return ScopeConfigInterface $scopeConfig
     */
    public function getScopeConfig() {
        return $this->scopeConfig;
    }
}
