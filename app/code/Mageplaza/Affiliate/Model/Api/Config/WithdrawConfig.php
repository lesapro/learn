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

use Mageplaza\Affiliate\Api\Data\Config\WithdrawConfigInterface;

/**
 * Class WithdrawConfig
 * @package Mageplaza\Affiliate\Model\Api\Config
 */
class WithdrawConfig extends \Magento\Framework\DataObject implements WithdrawConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAllowRequest()
    {
        return $this->getData(self::ALLOW_REQUEST);
    }

    /**
     * {@inheritdoc}
     */
    public function setAllowRequest($value)
    {
        return $this->setData(self::ALLOW_REQUEST, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethod()
    {
        return $this->getData(self::PAYMENT_METHOD);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentMethod($value)
    {
        return $this->setData(self::PAYMENT_METHOD, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMinimumBalance()
    {
        return $this->getData(self::MINIMUM_BALANCE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMinimumBalance($value)
    {
        return $this->setData(self::MINIMUM_BALANCE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMinimum()
    {
        return $this->getData(self::MINIMUM);
    }

    /**
     * {@inheritdoc}
     */
    public function setMinimum($value)
    {
        return $this->setData(self::MINIMUM, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaximum()
    {
        return $this->getData(self::MAXIMUM);
    }

    /**
     * {@inheritdoc}
     */
    public function setMaximum($value)
    {
        return $this->setData(self::MAXIMUM, $value);
    }
}
