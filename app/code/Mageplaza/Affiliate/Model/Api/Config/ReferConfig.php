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

use Mageplaza\Affiliate\Api\Data\Config\ReferConfigInterface;

/**
 * Class ReferConfig
 * @package Mageplaza\Affiliate\Model\Api\Config
 */
class ReferConfig extends \Magento\Framework\DataObject implements ReferConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEnable()
    {
        return $this->getData(self::ENABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnable($value)
    {
        return $this->setData(self::ENABLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccountSharing()
    {
        return $this->getData(self::ACCOUNT_SHARING);
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountSharing($value)
    {
        return $this->setData(self::ACCOUNT_SHARING, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLink()
    {
        return $this->getData(self::DEFAULT_LINK);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultLink($value)
    {
        return $this->setData(self::DEFAULT_LINK, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSharingContent()
    {
        return $this->getData(self::SHARING_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSharingContent($value)
    {
        return $this->setData(self::SHARING_CONTENT, $value);
    }
}
