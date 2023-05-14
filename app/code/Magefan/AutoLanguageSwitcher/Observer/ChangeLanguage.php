<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\AutoLanguageSwitcher\Observer;

/**
 * Class Change Language
 *
 * @package Magefan\AutoLanguageSwitcher\Observer
 */
class ChangeLanguage implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magefan\AutoLanguageSwitcher\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $actionFlag;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * ChangeLanguage constructor.
     * @param \Magefan\AutoLanguageSwitcher\Model\Config $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Store\Api\StoreCookieManagerInterface|null $storeCookieManager
     * @param \Magento\Framework\App\ProductMetadata|null $productMetadata
     * @param \Magento\Framework\App\RequestInterface|null $request
     */
    public function __construct(
        \Magefan\AutoLanguageSwitcher\Model\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Store\Api\StoreCookieManagerInterface $storeCookieManager = null,
        \Magento\Framework\App\ProductMetadata $productMetadata = null,
        \Magento\Framework\App\RequestInterface $request = null
    ) {
        $this->customerSession = $customerSession;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->actionFlag = $actionFlag;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->storeCookieManager = $storeCookieManager ?: $objectManager->get(
            \Magento\Store\Api\StoreCookieManagerInterface::class
        );

        $this->productMetadata = $productMetadata ?: $objectManager->get(
            \Magento\Framework\App\ProductMetadata::class
        );

        $this->request = $request ?: $objectManager->get(
            \Magento\Framework\App\RequestInterface::class
        );
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->config->isEnabled()
            && $this->config->isAutoRedirectEnabled()
            && $this->config->isAllowedOnPage()
        ) {
            $languageCode = $this->config->getBrowserLanguageCode();
            $sKey = 'mf_lang_redirect';
            //$store = $this->config->getStoreByCountry();
            if ($languageCode === $this->customerSession->getData($sKey)) {
                return;
                /*
                if (!($store
                    && $store->isUseStoreInUrl()
                    && $this->request->getFullActionName() == 'cms_index_index'
                    && stripos($store->getBaseUrl(), '/' . $store->getCode()) !== false)) {
                    return;
                }
                */
            }
            $this->customerSession->setData($sKey, $languageCode);

            $store = $this->config->getStoreByCountry();
            if ($store && $store->getId() !== $this->storeManager->getStore()->getId()) {
                $this->storeCookieManager->setStoreCookie($store);

                $url = str_replace('&amp;', '&', $store->getCurrentUrl(true));
                $response = $observer->getEvent()->getData('controller_action')->getResponse();
                $this->actionFlag->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_DISPATCH, true);
                if (version_compare($this->productMetadata->getVersion(), '2.3') < 0) {
                    $response->setRedirect($url)->sendResponse();
                } else {
                    $params = http_build_query([
                        '___from_store' => $this->storeManager->getStore()->getCode(),
                        '___store' => $store->getCode(),
                        'uenc' => base64_encode($url)
                    ]);
                    $redirectUrl = $store->getUrl('stores/store/redirect');
                    $redirectUrl .= (strpos($redirectUrl, '?') === false) ? '?' : '&';
                    $redirectUrl .= $params;

                    $response->setRedirect($redirectUrl)->sendResponse();
                }
            }
        }
    }
}
