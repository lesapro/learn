<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Affiliate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Affiliate\Model\Api\Config;

use Mageplaza\Affiliate\Api\Data\Config\GeneralConfigInterface;

/**
 * Class GeneralConfig
 * @package Mageplaza\Affiliate\Model\Api\Config
 */
class GeneralConfig extends \Magento\Framework\DataObject implements GeneralConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->getData(self::ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($value)
    {
        return $this->setData(self::ENABLED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnableBanner()
    {
        return $this->getData(self::ENABLE_BANNER);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnableBanner($value)
    {
        return $this->setData(self::ENABLE_BANNER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiredTime()
    {
        return $this->getData(self::EXPIRED_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiredTime($value)
    {
        return $this->setData(self::EXPIRED_TIME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getOverwriteCookies()
    {
        return $this->getData(self::OVERWRITE_COOKIES);
    }

    /**
     * {@inheritdoc}
     */
    public function setOverwriteCookies($value)
    {
        return $this->setData(self::OVERWRITE_COOKIES, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUseCodeAsCoupon()
    {
        return $this->getData(self::USE_CODE_AS_COUPON);
    }

    /**
     * {@inheritdoc}
     */
    public function setUseCodeAsCoupon($value)
    {
        return $this->setData(self::USE_CODE_AS_COUPON, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getShowLink()
    {
        return $this->getData(self::SHOW_LINK);
    }

    /**
     * {@inheritdoc}
     */
    public function setShowLink($value)
    {
        return $this->setData(self::SHOW_LINK, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageWelcome()
    {
        return $this->getData(self::PAGE_WELCOME);
    }

    /**
     * {@inheritdoc}
     */
    public function setPageWelcome($value)
    {
        return $this->setData(self::PAGE_WELCOME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlType()
    {
        return $this->getData(self::URL_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlType($value)
    {
        return $this->setData(self::URL_TYPE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlPrefix()
    {
        return $this->getData(self::URL_PREFIX);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlPrefix($value)
    {
        return $this->setData(self::URL_PREFIX, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlParam()
    {
        return $this->getData(self::URL_PARAM);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlParam($value)
    {
        return $this->setData(self::URL_PARAM, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlCodeLength()
    {
        return $this->getData(self::URL_CODE_LENGTH);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlCodeLength($value)
    {
        return $this->setData(self::URL_CODE_LENGTH, $value);
    }
}
