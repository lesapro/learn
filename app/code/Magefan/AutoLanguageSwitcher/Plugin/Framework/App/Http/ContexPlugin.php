<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\AutoLanguageSwitcher\Plugin\Framework\App\Http;

use Magento\Framework\App\Http\Context;
use Magefan\AutoLanguageSwitcher\Model\Config;

/**
 * Class ContexPlugin
 *
 * @package Magefan\AutoCurrencySwitcher\Plugin\Framework\App\Http
 */
class ContexPlugin
{

    const CONTEXT_STORE = 'store';

    /**
     * @var \Magefan\AutoCurrencySwitcher\Model\Config
     */
    protected $config;

    /**
     * @var mixed
     */
    protected $cookieMetadataFactory;

    /**
     * @var mixed
     */
    protected $cookieManager;
    
    /**
     * @var null
     */
    protected $firstVisit = null;

    /**s
     * ContexPlugin constructor.
     *
     * @param \Magefan\AutoCurrencySwitcher\Model\Config $config
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     */
    public function __construct(
        Config $config,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory = null,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager = null
    )
    {
        $this->config = $config;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->cookieMetadataFactory = $cookieMetadataFactory ?: $objectManager->get(
            \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
        );
        $this->cookieManager = $cookieManager ?: $objectManager->get(
            \Magento\Framework\Stdlib\CookieManagerInterface::class
        );

    }

    /**
     * @param Context $subject
     * @param $result
     * @return mixed
     */
    public function afterGetData(Context $subject, $result)
    {
        if (empty($result[self::CONTEXT_STORE])
            && $this->config->isEnabled()
            && $this->config->isAutoRedirectEnabled()
            && $this->config->isAllowedOnPage()
        ) {

            if ($this->isFirstVisit()) {
                $store = $this->config->getStoreByCountry();
                if ($store) {
                    $result[self::CONTEXT_STORE] = $store->getCode();

                }
            }
        }

        return $result;
    }

    /**
     * @return bool|null
     */
    public function isFirstVisit()
    {
       // return true;
        if (null === $this->firstVisit ) {

            $cooieKey = 'mffirstvis';
            $this->firstVisit = !$this->cookieManager->getCookie($cooieKey);
            if ($this->firstVisit) {
                $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                    ->setHttpOnly(true)
                    ->setDurationOneYear()
                    ->setPath('/');

                $this->cookieManager->setPublicCookie($cooieKey, 1, $cookieMetadata);
            }
        }

        return $this->firstVisit;
    }
}
