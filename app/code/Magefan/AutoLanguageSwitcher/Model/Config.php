<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\AutoLanguageSwitcher\Model;

use Magento\Store\Model\ScopeInterface;

/**
 * Class Config Model
 */
class Config
{

    /**
     * @var string
     */
    const XML_PATH_AUTOLANGUAGESWITCHER_ALLOWED_PAGES = 'mfautolanguageswitcher/extension_restrictions/allowed_pages';

    /**
     * @var string
     */
    const XML_PATH_AUTOLANGUAGESWITCHER_SPECIFIC_PAGES = 'mfautolanguageswitcher/extension_restrictions/specific_pages';

    /**
     * @var string
     */
    const XML_PATH_AUTOLANGUAGESWITCHER_SWITCHER_MODE = 'mfautolanguageswitcher/general/switcher_mode';

    /**
     * @var string
     */
    const XML_PATH_AUTOLANGUAGESWITCHER_SUGGESTION_POPUP_TEXT = 'mfautolanguageswitcher/general/suggestion_popup_text';

    /**
     * @var string
     */
    const XML_PATH_AUTOLANGUAGESWITCHER_ENABLED = 'mfautolanguageswitcher/general/enabled';

    /**
     * @var string
     */
    const XML_PATH_GET_USER_AGENT = 'mfautolanguageswitcher/extension_restrictions/user_agent';

    /**
     * @var string
     */
    const XML_PATH_GET_IPS = 'mfautolanguageswitcher/extension_restrictions/ips';

    /**
     * @var string
     */
    const XML_PATH_GET_DISPLAY_LANGUAGE = 'mfautolanguageswitcher/display_language';

    /**
     * @var string
     */
    const XML_PATH_AUTOLANGUAGESWITCHER_SHARE = 'mfautolanguageswitcher/general/share_storeview';

    /**
     * @var string
     */
    const XML_PATH_GENERAL_LOCAL_CODE = 'general/locale/code';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\HTTP\Header
     */
    protected $httpHeader;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magefan\GeoIp\Model\IpToCountryRepository
     */
    protected $ipToCountryRepository;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $displayLanguage;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var bool
     */
    protected $allowedOnPage;

    /**
     * Config constructor.
     * @param \Magento\Framework\App\RequestInterface $httpRequest
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magefan\GeoIp\Model\IpToCountryRepository $ipToCountryRepository
     * @param \Magento\Framework\HTTP\Header $httpHeader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $httpRequest,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magefan\GeoIp\Model\IpToCountryRepository $ipToCountryRepository,
        \Magento\Framework\HTTP\Header $httpHeader,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->request = $httpRequest;
        $this->httpHeader = $httpHeader;
        $this->storeManager = $storeManager;
        $this->ipToCountryRepository = $ipToCountryRepository;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GET_DISPLAY_LANGUAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        if (null === $this->enabled) {
            $this->enabled = $this->scopeConfig->getValue(
                self::XML_PATH_AUTOLANGUAGESWITCHER_ENABLED,
                ScopeInterface::SCOPE_STORE
            ) && $this->userAgentAllowed() && $this->ipAllowed();
        }

        return $this->enabled;
    }

    /**
     * @return bool
     */
    protected function userAgentAllowed()
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();
        $userAgentScope = $this->scopeConfig->getValue(
            self::XML_PATH_GET_USER_AGENT,
            ScopeInterface::SCOPE_STORE
        );

        $replace = str_replace("\r", "\n", $userAgentScope);
        $robots = explode("\n", $replace);
        foreach ($robots as $robot) {
            if (!trim($robot)) {
                continue;
            }
            if (strpos($userAgent, $robot) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function ipAllowed()
    {
        $ip = (string)$this->ipToCountryRepository->getRemoteAddress();
        $iPs = (string)$this->scopeConfig->getValue(
            self::XML_PATH_GET_IPS,
            ScopeInterface::SCOPE_STORE
        );
        $iPs = explode(',', $iPs);
        foreach ($iPs as $_ip) {
            if (!trim($_ip)) {
                continue;
            }
            if ($_ip == $ip) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return int|mixed
     */
    public function getStoreIdByCountry()
    {
        $countryCode = $this->ipToCountryRepository->getVisitorCountryCode();

        $countryCode = strtolower($countryCode);
        if (null === $this->displayLanguage) {
            $this->displayLanguage = [];
            $config = $this->scopeConfig->getValue(
                self::XML_PATH_GET_DISPLAY_LANGUAGE,
                ScopeInterface::SCOPE_STORE
            );

            if ($config) {
                foreach ($config as $region => $currencyByCountry) {
                    foreach ($currencyByCountry as $cc => $currency) {
                        $this->displayLanguage[$cc] = $currency;
                    }
                }
            }
        }

        $storeId = (isset($this->displayLanguage[$countryCode]))
            ? $this->displayLanguage[$countryCode]
            : 0;
        if (!$storeId) {
            $storeId = $this->getStoreIdByBrowserLanguage();
        }
        return $storeId;
    }

    /**
     * @return int
     */
    public function getStoreIdByBrowserLanguage()
    {
        if ($languageCode = $this->getBrowserLanguageCode()) {

            $languageCode1 = explode('_', $languageCode);
            $languageCode1 = $languageCode1[0];

            $shareStoreView = $this->scopeConfig->getValue(self::XML_PATH_AUTOLANGUAGESWITCHER_SHARE, ScopeInterface::SCOPE_STORE);
            if ($shareStoreView) {
                $storeList = $this->storeManager->getWebsite()->getStores();
            } else {
                $storeList = $this->storeManager->getStores();
            }

            $currentStore = $this->storeManager->getStore();

            $potentialStores = [
                'group' => 0,
                'website' => 0,
                'any' => 0
            ];

            foreach ([$languageCode, $languageCode1] as $browserLanguageCode) {

                foreach ($storeList as $store) {
                    if (!$store->getIsActive() || !$store->getId()) {
                        continue;
                    }
                    $storeLang = $this->getStoreLanguage($store->getId());

                    $storeLang1 = explode('_', $storeLang);
                    $storeLang1 = $storeLang1[0];

                    if ($storeLang == $browserLanguageCode || $storeLang1 == $browserLanguageCode) {
                        if ($this->getStoreLanguage($store->getId()) !== $this->getStoreLanguage($currentStore->getId())) {

                            if ($store->getGroupId() && $store->getGroupId() == $currentStore->getGroupId()) {
                                if (empty($potentialStores['group'])) {
                                    $potentialStores['group'] = $store->getId();
                                }
                            }

                            if ($store->getWebsiteId() && $store->getWebsiteId() == $currentStore->getWebsiteId()) {
                                if (empty($potentialStores['website'])) {
                                    $potentialStores['website'] = $store->getId();
                                }
                            }

                            if (empty($potentialStores['any'])) {
                                $potentialStores['any'] = $store->getId();
                            }
                        }
                    }
                }
            }

            foreach ($potentialStores as $storeId) {
                if ($storeId) {
                    return $storeId;
                }
            }
        }
        return 0;
    }

    /**
     * @return string
     */
    public function getStoreLanguage($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_LOCAL_CODE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return bool|\Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreByCountry()
    {
        $storeId = $this->getStoreIdByCountry();
        if ($storeId) {
            return $this->storeManager->getStore($storeId);
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getBrowserLanguageCode()
    {
        $languageCode = $this->request->getServer('HTTP_ACCEPT_LANGUAGE');
        if ($languageCode) {
            $languageCode = explode(',', $languageCode);
            $languageCode = str_replace('-', '_', $languageCode[0]);
            return $languageCode;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isSwitcherPopupEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AUTOLANGUAGESWITCHER_SWITCHER_MODE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isAutoRedirectEnabled()
    {
        return !$this->isSwitcherPopupEnabled();
    }

    /**
     * @return bool
     */
    public function isAllowedOnPage()
    {
        if (null !== $this->allowedOnPage) {
            return $this->allowedOnPage;
        }
        $this->allowedOnPage = false;

        $spType = $this->scopeConfig->getValue(
            self::XML_PATH_AUTOLANGUAGESWITCHER_ALLOWED_PAGES,
            ScopeInterface::SCOPE_STORE
        );

        if (!$spType) {
            return ($this->allowedOnPage = true);
        }

        $spPages = $this->scopeConfig->getValue(
            self::XML_PATH_AUTOLANGUAGESWITCHER_SPECIFIC_PAGES,
            ScopeInterface::SCOPE_STORE
        );
        $spPages = explode("\n", str_replace("\r", "\n", $spPages));

        foreach ($spPages as $key => $path) {
            $spPages[$key] = trim($spPages[$key]);
            if (empty($spPages[$key])) {
                unset($spPages[$key]);
            }
        }
        $baseUrl = trim($this->storeManager->getStore()->getBaseUrl(), '/');
        $baseUrl = str_replace('/index.php', '', $baseUrl);

        $currentUrl = $this->storeManager->getStore()->getCurrentUrl();
        $currentUrl = explode('?', $currentUrl);
        $currentUrl = trim($currentUrl[0], '/');
        foreach (['index.php', '.php', '.html'] as $end) {
            $el = mb_strlen($end);
            $cl = mb_strlen($currentUrl);
            if (mb_strrpos($currentUrl, $end) == $cl - $el) {
                $currentUrl = mb_substr($currentUrl, 0, $cl - $el);
            }
        }
        $currentUrl = str_replace('/index.php', '', $currentUrl);
        $currentUrl = trim($currentUrl, '/');
        foreach ($spPages as $key => $path) {
            $path = trim($path, '/');

            if (mb_strlen($path)) {
                if ('*' == $path[0]) {
                    $subPath = trim($path, '*/');
                    if (mb_strlen($currentUrl) - mb_strlen($subPath) === mb_strrpos($currentUrl, $subPath)) {
                        $this->allowedOnPage = true;
                        break;
                    }
                }

                if ('*' == $path[mb_strlen($path) - 1]) {
                    if (0 === mb_strpos($currentUrl, $baseUrl . '/' . trim($path, '*/'))) {
                        $this->allowedOnPage = true;
                        break;
                    }
                }
                if ($currentUrl == $baseUrl . '/' . trim($path, '/')) {
                    $this->allowedOnPage = true;
                    break;
                }
            } else {
                //homepage

                if ($currentUrl == $baseUrl) {
                    $this->allowedOnPage = true;
                    break;
                }
            }
        }

        if (2 == $spType) {
            $this->allowedOnPage = !$this->allowedOnPage;
        }

        return $this->allowedOnPage;
    }


    public function getSuggestionPopupText($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AUTOLANGUAGESWITCHER_SUGGESTION_POPUP_TEXT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
