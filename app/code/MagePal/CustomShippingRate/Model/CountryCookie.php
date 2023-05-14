<?php

namespace MagePal\CustomShippingRate\Model;

class CountryCookie
{
    protected $cookieManager;
    protected $cookieMetadataFactory;

    const COUNTRY_KEY = 'country';
    const COUNTRY_NAME = 'countryName';

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    )
    {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * get cookie by name
     */
    public function getCookie($cookieName) {
        return $this->cookieManager->getCookie($cookieName);

    }

    /**
     * set public cookie
     */
    public function setPublicCookie($cookieName, $value)
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(86400) // Cookie will expire after one day (86400 seconds)
            ->setSecure(true) //the cookie is only available under HTTPS
            ->setPath('/')// The cookie will be available to all pages and subdirectories within the /subfolder path
            ->setHttpOnly(false); // cookies can be accessed by JS

        $this->cookieManager->setPublicCookie(
            $cookieName,
            $value,
            $metadata
        );
    }
}
