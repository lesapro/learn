<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GeoipRedirect
 */


namespace Amasty\GeoipRedirect\Plugin;

use Amasty\GeoipRedirect\Model\Source\Logic;
use Magento\Store\Api\StoreResolverInterface;
use Magento\Store\Model\ScopeInterface;

class Action
{
    const LOCAL_IP = '127.0.0.1';
    const URL_TRIM_CHARACTER = '/';

    protected $redirectAllowed = false;

    protected $addressPath = [
        'HTTP_X_REAL_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR'
    ];

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Amasty\GeoipRedirect\Helper\Data
     */
    protected $geoipHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Amasty\Geoip\Model\Geolocation
     */
    protected $geolocation;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Api\StoreCookieManagerInterface
     */
    protected $storeCookieManager;

    /**
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface $sessionManager
     */
    protected $sessionManager;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $resolver;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * Action constructor.
     *
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Amasty\GeoipRedirect\Helper\Data $geoipHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Amasty\Geoip\Model\Geolocation $geolocation
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Api\StoreCookieManagerInterface $storeCookieManager
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param \Magento\Framework\Locale\ResolverInterface $resolver
     * @param \Amasty\Base\Model\Serializer $serializer
     */
    public function __construct(
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\GeoipRedirect\Helper\Data $geoipHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Geoip\Model\Geolocation $geolocation,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Api\StoreCookieManagerInterface $storeCookieManager,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Framework\Locale\ResolverInterface $resolver,
        \Amasty\Base\Model\Serializer $serializer
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->scopeConfig = $scopeConfig;
        $this->geoipHelper = $geoipHelper;
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->geolocation = $geolocation;
        $this->customerSession = $customerSession;
        $this->storeCookieManager = $storeCookieManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->response = $response;
        $this->sessionManager = $sessionManager;
        $this->resolver = $resolver;
        $this->serializer = $serializer;
    }

    /**
     * @param \Magento\Framework\App\FrontControllerInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function aroundDispatch(
        \Magento\Framework\App\FrontControllerInterface $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $currentStoreId = $this->storeManager->getStore()->getId();
        $scopeStores = ScopeInterface::SCOPE_STORES;
        if (!$this->scopeConfig->isSetFlag('amgeoipredirect/general/enable', $scopeStores, $currentStoreId)
            || $this->isNeedToProceed($request)
        ) {
            return $proceed($request);
        }

        $session = $this->sessionManager;
        $countRedirectStore = $countRedirectCurrency = $countRedirectUrl = 0;
        $isNotFirstTime = null;
        $changeCurrency = false;

        if ($this->scopeConfig->getValue('amgeoipredirect/restriction/first_visit_redirect')) {
            // session value getters should be before processed request, otherwise will return null with FPC enabled
            $isNotFirstTime = $session->getIsNotFirstTime();
            $countRedirectStore = $session->getAmYetRedirectStore();
            $countRedirectCurrency = $session->getAmYetRedirectCurrency();
            $countRedirectUrl = $session->getAmYetRedirectUrl();
            $session->setIsNotFirstTime(1);
        }

        if ($this->scopeConfig->getValue('amgeoipredirect/restriction/apply_logic') == Logic::HOMEPAGE_ONLY) {
            $result = $proceed($request);
        }

        $this->applyLogic($request);

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->scopeConfig->getValue('amgeoipredirect/general/enable', $scopeStores, $currentStoreId)
            && $this->redirectAllowed
        ) {
            $currentIp = $this->getCurrentIp($request);
            if ($this->isIpBlock($currentIp)) {
                $websiteId = $this->storeManager->getWebsite()->getId();
                $page = $this->scopeConfig->getValue(
                    'amgeoipredirect/restrict_by_ip/cms_to_show',
                    ScopeInterface::SCOPE_WEBSITE,
                    $websiteId
                );
                $url = $this->urlBuilder->getUrl($page);
                if (rtrim(parse_url($url)['path'], '/')
                    !== rtrim(parse_url($this->urlBuilder->getCurrentUrl())['path'], '/')
                ) {
                    $this->response->setNoCacheHeaders();
                    return $resultRedirect->setUrl($url);
                }
            }
            $location = $this->geolocation->locate($currentIp);
            $country = $location->getCountry();

            if (!$countRedirectCurrency
                && !$isNotFirstTime
                && $this->scopeConfig->getValue('amgeoipredirect/country_currency/enable_currency')
            ) {
                $changeCurrency = true;
            }

            if (!$countRedirectUrl
                && !$isNotFirstTime
                && $this->scopeConfig->getValue('amgeoipredirect/country_url/enable_url')
                && $country
            ) {
                $urlMapping = $this->serializer->unserialize(
                    $this->scopeConfig->getValue(
                        'amgeoipredirect/country_url/url_mapping',
                        $scopeStores,
                        $currentStoreId
                    )
                );

                $currentUrl = trim($this->urlBuilder->getCurrentUrl(), self::URL_TRIM_CHARACTER);

                foreach ($urlMapping as $countries => $url) {
                    $url = trim($url, self::URL_TRIM_CHARACTER);

                    if (strpos($countries, $country) !== false && $url != $currentUrl) {
                        $session->setAmYetRedirectUrl(1);
                        $this->response->setNoCacheHeaders();

                        if ($this->needShowRedirectionPopup()) {
                            $session->setAmYetRedirectUrl(null);
                            $session->setIsNotFirstTime(null);
                            return $proceed($request);
                        }

                        if ($this->sessionManager->getWillRedirect() !== false) {
                            return $resultRedirect->setUrl($url);
                        }
                    }
                }
            }

            if (!$countRedirectStore
                && !$isNotFirstTime
                && $this->scopeConfig->getValue('amgeoipredirect/country_store/enable_store')
            ) {
                $allStores = $this->storeManager->getStores();
                foreach ($allStores as $store) {
                    $currentStoreUrl = str_replace('&amp;', '&', $store->getCurrentUrl(false));
                    $redirectStoreUrl = trim($currentStoreUrl, '/');

                    $countries = $this->scopeConfig->getValue(
                        'amgeoipredirect/country_store/affected_countries',
                        $scopeStores,
                        $store->getId()
                    );
                    if (!$this->scopeConfig->getValue('amgeoipredirect/restriction/redirect_between_websites')) {
                        $useMultistores = $store->getWebsiteId() == $this->storeManager->getStore()->getWebsiteId();
                    } else {
                        $useMultistores = true;
                    }

                    if ($country && $countries && strpos($countries, $country) !== false
                        && $store->getId() != $currentStoreId
                        && $useMultistores
                    ) {
                        $currentUrl = $this->urlBuilder->getCurrentUrl();
                        $neededBaseUrl = $store->getBaseUrl();
                        if ((strpos($currentUrl, $neededBaseUrl) === false)
                            || !$this->_compareEqualUrlFromStore($request, $store)
                        ) {
                            if ($changeCurrency) {
                                $this->_setNewCurrencyIfExist($country, $scopeStores, $store->getId());
                            }
                            $this->_setDefaultLocale($store);
                            $session->setAmYetRedirectStore(1);
                            $this->storeCookieManager->setStoreCookie($store);
                            $this->response->setNoCacheHeaders();

                            $fromUrl = '___from_store=' . $store->getCode();
                            $redirectStoreUrl .= (parse_url($redirectStoreUrl, PHP_URL_QUERY) ? '&' : '?') . $fromUrl;

                            if ($this->needShowRedirectionPopup()) {
                                $session->setAmYetRedirectStore(null);
                                $session->setIsNotFirstTime(null);
                                return $proceed($request);
                            }

                            if ($this->sessionManager->getWillRedirect() !== false) {
                                return $resultRedirect->setUrl($redirectStoreUrl);
                            }
                        }
                    }
                }
            }

            if ($changeCurrency && !empty($country)) {
                $this->_setNewCurrencyIfExist($country, $scopeStores, $currentStoreId);
            }
        }

        if (isset($result)) {
            return $result;
        }

        return $proceed($request);
    }

    protected function needShowRedirectionPopup()
    {
        $websiteId = $this->storeManager->getWebsite()->getId();
        $needPopup = $this->scopeConfig->getValue(
            'amgeoipredirect/general/redirection_decline',
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        if ($needPopup && $this->sessionManager->getNeedShow() !== false) {
            $this->sessionManager->setNeedShow(true);
            return true;
        }

        return false;
    }

    /**
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return bool
     */
    protected function _setDefaultLocale($store)
    {
        if ($store->getId()) {
            $localeCode = $this->scopeConfig->getValue(
                'general/locale/code',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getId()
            );

            $this->resolver->setDefaultLocale($localeCode)->setLocale($localeCode);
        } else {
            return false;
        }
    }

    /**
     * @param $country
     * @param $scopeStores
     * @param $currentStoreId
     * @return $this
     */
    protected function _setNewCurrencyIfExist($country, $scopeStores, $currentStoreId)
    {
        $currencyMapping = $this->serializer->unserialize(
            $this->scopeConfig->getValue(
                'amgeoipredirect/country_currency/currency_mapping',
                $scopeStores,
                $currentStoreId
            )
        );

        foreach ($currencyMapping as $countries => $currency) {
            if (strpos($countries, $country) !== false
                && $this->storeManager->getStore()
                    ->getCurrentCurrencyCode() != $currency
            ) {
                $this->sessionManager->setAmYetRedirectCurrency(1);
                $this->storeManager->getStore()->setCurrentCurrencyCode($currency);
            }
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param $checkStore
     * @return bool
     */
    protected function _compareEqualUrlFromStore($request, $checkStore)
    {
        if ($request->getParam(StoreResolverInterface::PARAM_NAME)) {

            return ($checkStore
                && ($request->getParam(StoreResolverInterface::PARAM_NAME) != $checkStore->getCode()))
                ? false : true;
        }

        return false;
    }

    /**
     * Is redirect allowed
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return string
     */
    protected function applyLogic($request)
    {
        $applyLogic = $this->scopeConfig->getValue('amgeoipredirect/restriction/apply_logic');
        $currentUrl = $this->urlBuilder->getCurrentUrl();
        switch ($applyLogic) {
            case Logic::SPECIFIED_URLS:
                $acceptedUrls = explode(
                    PHP_EOL,
                    $this->scopeConfig->getValue('amgeoipredirect/restriction/accepted_urls')
                );
                foreach ($acceptedUrls as $url) {
                    $url = trim($url);
                    if ($url && $currentUrl && $this->_compareUrls($currentUrl, $url)) {
                        $this->redirectAllowed = true;
                        return $url;
                    }
                }
                break;
            case Logic::HOMEPAGE_ONLY:
                if ($request->getRouteName() == 'cms' && $request->getActionName() == 'index') {
                    $this->redirectAllowed = true;
                }
                break;
            default:
                $exceptedUrls = explode(
                    PHP_EOL,
                    $this->scopeConfig->getValue('amgeoipredirect/restriction/excepted_urls')
                );
                foreach ($exceptedUrls as $url) {
                    $url = trim($url);
                    if ($url && $currentUrl && strpos($currentUrl, $url) !== false) {
                        $this->redirectAllowed = false;
                        return $url;
                    } else {
                        $this->redirectAllowed = true;
                    }
                }
        }

        return '';
    }

    /**
     * @param string $currentUrl
     * @param string $comapareUrl
     * @return bool
     */
    protected function _compareUrls($currentUrl, $comapareUrl)
    {
        $urlParse = parse_url($comapareUrl);
        $currentUrlParse = parse_url($currentUrl);

        return (is_array($urlParse)
            && is_array($currentUrlParse)
            && (!isset($urlParse['host']) || $urlParse['host'] === $currentUrlParse['host'])
            && (str_replace('/', '', $urlParse['path']) === str_replace('/', '', $currentUrlParse['path']))
        );
    }

    /**
     * Is redirect by GeoIP has not needed
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return bool
     */
    protected function isNeedToProceed($request)
    {
        if ($this->isIpRestricted($request)
            || $request->isXmlHttpRequest()
        ) {
            return true;
        }
        $isApi = $request->getControllerModule() == 'Mage_Api';
        if ($isApi || !$this->geoipHelper->isModuleOutputEnabled('Amasty_Geoip')) {
            return true;
        }
        $userAgent = $request->getHeader('USER_AGENT');
        $userAgentsIgnore = $this->scopeConfig->getValue('amgeoipredirect/restriction/user_agents_ignore');
        if ($userAgent && !empty($userAgentsIgnore)) {
            $userAgentsIgnore = array_map("trim", explode(',', $userAgentsIgnore));
            foreach ($userAgentsIgnore as $agent) {
                if ($agent && stripos($userAgent, $agent) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * is IP in restricted list
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return bool
     */
    private function isIpRestricted($request)
    {
        $ipRestriction = $this->scopeConfig->getValue('amgeoipredirect/restriction/ip_restriction');
        $currentIp = $this->getCurrentIp($request);
        if ($currentIp && !empty($ipRestriction)) {
            $ipRestriction = array_map("rtrim", explode(PHP_EOL, $ipRestriction));
            foreach ($ipRestriction as $ip) {
                if ($currentIp == $ip) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return string
     */
    private function getCurrentIp($request)
    {
        foreach ($this->addressPath as $path) {
            $ip = $request->getServer($path);
            if ($ip) {
                if (strpos($ip, ',') !== false) {
                    $addresses = explode(',', $ip);
                    foreach ($addresses as $address) {
                        if (trim($address) != self::LOCAL_IP) {
                            return trim($address);
                        }
                    }
                } else {
                    if ($ip != self::LOCAL_IP) {
                        return $ip;
                    }
                }
            }

        }

        return $this->remoteAddress->getRemoteAddress();
    }

    /**
     * @param string $userIp
     *
     * @return bool
     */
    private function isIpBlock($userIp)
    {
        $websiteId = $this->storeManager->getWebsite()->getId();
        $configIpsToBlock = $this->scopeConfig->getValue("amgeoipredirect/restrict_by_ip/ip_to_block");
        $websiteIpsToBlock = $this->scopeConfig->getValue(
            "amgeoipredirect/restrict_by_ip/ip_to_block",
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        if (empty($websiteIpsToBlock) && empty($configIpsToBlock)) {
            return false;
        }
        $ipsWeb = preg_split('/\n|\r\n?/', $websiteIpsToBlock);
        $ipsConfig = preg_split('/\n|\r\n?/', $configIpsToBlock);
        $ips = array_unique(array_merge($ipsWeb, $ipsConfig));

        foreach($ips as $ip) {
            if (trim($ip) === $userIp) {
                return true;
            }
        }

        return false;
    }
}
