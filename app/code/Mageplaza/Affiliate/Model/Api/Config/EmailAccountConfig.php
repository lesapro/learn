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

use Mageplaza\Affiliate\Api\Data\Config\EmailAccountConfigInterface;

/**
 * Class EmailAccountConfig
 * @package Mageplaza\Affiliate\Model\Api\Config
 */
class EmailAccountConfig extends \Magento\Framework\DataObject implements EmailAccountConfigInterface
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
    public function getWelcome()
    {
        return $this->getData(self::WELCOME);
    }

    /**
     * {@inheritdoc}
     */
    public function setWelcome($value)
    {
        return $this->setData(self::WELCOME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getApprove()
    {
        return $this->getData(self::APPROVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setApprove($value)
    {
        return $this->setData(self::APPROVE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnableRejection()
    {
        return $this->getData(self::ENABLE_REJECTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnableRejection($value)
    {
        return $this->setData(self::ENABLE_REJECTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRejectionTemplate()
    {
        return $this->getData(self::REJECTION_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setRejectionTemplate($value)
    {
        return $this->setData(self::REJECTION_TEMPLATE, $value);
    }


    /**
     * {@inheritdoc}
     */
    public function getEnableStatus()
    {
        return $this->getData(self::ENABLE_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnableStatus($value)
    {
        return $this->setData(self::ENABLE_STATUS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusTemplate()
    {
        return $this->getData(self::STATUS_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatusTemplate($value)
    {
        return $this->setData(self::STATUS_TEMPLATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnableWithdrawCancel()
    {
        return $this->getData(self::ENABLE_WITHDRAW_CANCEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnableWithdrawCancel($value)
    {
        return $this->setData(self::ENABLE_WITHDRAW_CANCEL, $value);
    }
}
