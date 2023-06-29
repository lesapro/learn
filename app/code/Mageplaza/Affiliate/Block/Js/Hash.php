<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
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

namespace Mageplaza\Affiliate\Block\Js;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Affiliate\Helper\Data;
use Mageplaza\Affiliate\Model\Config\Source\Urltype;

/**
 * Class Hash
 * @package Mageplaza\Affiliate\Block\Js
 */
class Hash extends Template
{
    /**
     * @var Data
     */
    protected $_affiliateHelper;

    /**
     * Hash constructor.
     *
     * @param Context $context
     * @param Data $affiliateHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $affiliateHelper,
        array $data = []
    ) {
        $this->_affiliateHelper = $affiliateHelper;

        parent::__construct($context, $data);
    }

    /**
     * Get prefix
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->_affiliateHelper->getUrlPrefix();
    }

    /**
     * Get cookie name
     * @return string
     */
    public function getCookieName()
    {
        return Data::AFFILIATE_COOKIE_NAME;
    }

    /**
     * @return bool
     */
    public function checkCookie()
    {
        $urlType = $this->_affiliateHelper->getUrlType();

        return Urltype::TYPE_HASH === $urlType;
    }

    /**
     * @return float|int
     */
    public function getExpire()
    {
        $expireDay = (int)$this->_affiliateHelper->getExpiredTime();

        return $expireDay * 24 * 3600;
    }

    /**
     * @return mixed
     */
    public function getConfigCustomAffiliate()
    {
        $customCssConfig = $this->_affiliateHelper->getCustomCss();

        return $customCssConfig;
    }
}
