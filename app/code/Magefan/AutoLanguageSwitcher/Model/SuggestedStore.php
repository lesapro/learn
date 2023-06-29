<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\AutoLanguageSwitcher\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Config\Model\Config\Source\Locale;
use Magefan\AutoLanguageSwitcher\Api\SuggestedStoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class SuggestedStore
 * @package Magefan\AutoLanguageSwitcher\Model
 */
class SuggestedStore implements SuggestedStoreInterface
{
    /**
     * @var string
     */
    const XML_PATH_GENERAL_LOCAL_CODE = 'general/locale/code';

    /**
     * @var \Magefan\AutoLanguageSwitcher\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Config\Model\Config\Source\Locale
     */
    protected $locale;

    /**
     * @var EncoderInterface
     */
    protected $encoder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Emulation
     */
    protected $emulation;

    /**
     * SuggestedStoreCode constructor.
     * @param \Magefan\AutoLanguageSwitcher\Model\Config $config
     */
    public function __construct(
        Config $config,
        ScopeConfigInterface $scopeConfig,
        Locale $locale,
        EncoderInterface $encoder,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        RequestInterface $request,
        \Magento\Store\Model\App\Emulation $emulation = null
    ) {
        $this->config = $config;
        $this->locale = $locale;
        $this->scopeConfig = $scopeConfig;
        $this->encoder = $encoder;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->emulation = $emulation ?: $objectManager->get(\Magento\Store\Model\App\Emulation::class);
    }

    /**
     * @return string
     */
    public function get()
    {
        $result = [];
        if ($this->config->isEnabled()) {
            if ($store = $this->config->getStoreByCountry()) {
                $currentStore = $this->storeManager->getStore();
                if ($store->getId() != $currentStore->getId()) {
                    $languageCode = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_LOCAL_CODE, ScopeInterface::SCOPE_STORE, $store->getId());

                    $this->emulation->startEnvironmentEmulation($store->getId());
                    $locales = $this->locale->toOptionArray();
                    $yesLabel = (string)__('Yes');
                    $noLabel = (string)__('No');
                    $this->emulation->stopEnvironmentEmulation();

                    $localeCode = $localeLabel = '';

                    foreach ($locales as $key => $options) {
                        if ($languageCode == $options['value']) {
                            $localeCode = $options['value'];
                            $localeLabel = $options['label'];
                        }
                    }

                    $redirectUrl = $this->urlBuilder->getUrl('stores/store/switch');
                    $redirectUrl = str_replace(
                        ['___from_store', '___store'],
                        ['___from_store_old', '___store_old'],
                        $redirectUrl
                    );


                    $currentUrl = $this->request->getParam('current_url');
                    if ($currentUrl) {
                        $currentUrl = base64_decode($currentUrl);

                        $currentUrl = str_replace('___store=' . $currentStore->getCode(), '', $currentUrl);
                        if (false === strpos($currentUrl, $store->getBaseUrl())) {
                            $currentUrl = str_replace($currentStore->getBaseUrl(), $store->getBaseUrl(), $currentUrl);
                        }

                        $currentUrl = trim($currentUrl, '?');
                        $currentUrl = base64_encode($currentUrl);
                    }


                    $redirectUrl .= ((false === strpos($redirectUrl, '?')) ? '?' : '&');
                    $redirectUrl .= '___store=' . $store->getCode() .
                        '&___from_store=' . $currentStore->getCode() .
                        '&' . ActionInterface::PARAM_NAME_URL_ENCODED . '=' . $currentUrl;


                    $result = [
                        'code' => $store->getCode(),
                        'id' => $store->getId(),
                        'locale_code' => $localeCode,
                        'locale_label' => $localeLabel,
                        'message' => __($this->config->getSuggestionPopupText($store->getId()), $localeLabel),
                        'redirect_url' => $redirectUrl,
                        'yes_label' => $yesLabel,
                        'no_label' => $noLabel,
                    ];
                }
            }
        }
        return json_encode($result);
    }
}
