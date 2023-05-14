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

use Mageplaza\Affiliate\Api\Data\Config\EmailConfigInterface;

/**
 * Class EmailConfig
 * @package Mageplaza\Affiliate\Model\Api\Config
 */
class EmailConfig extends \Magento\Framework\DataObject implements EmailConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSender()
    {
        return $this->getData(self::SENDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setSender($value)
    {
        return $this->setData(self::SENDER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdmin()
    {
        return $this->getData(self::ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdmin($value)
    {
        return $this->setData(self::ADMIN, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccount()
    {
        return $this->getData(self::ACCOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAccount($value)
    {
        return $this->setData(self::ACCOUNT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransaction()
    {
        return $this->getData(self::TRANSACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setTransaction($value)
    {
        return $this->setData(self::TRANSACTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getWithdraw()
    {
        return $this->getData(self::WITHDRAW);
    }

    /**
     * {@inheritdoc}
     */
    public function setWithdraw($value)
    {
        return $this->setData(self::WITHDRAW, $value);
    }
}
