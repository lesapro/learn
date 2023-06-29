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

use Mageplaza\Affiliate\Api\Data\Config\EmailAdminConfigInterface;

/**
 * Class EmailConfig
 * @package Mageplaza\Affiliate\Model\Api\Config
 */
class EmailAdminConfig extends \Magento\Framework\DataObject implements EmailAdminConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEmailsTo()
    {
        return $this->getData(self::EMAILS_TO);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailsTo($value)
    {
        return $this->setData(self::EMAILS_TO, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnableSignUp()
    {
        return $this->getData(self::ENABLE_SIGN_UP);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnableSignUp($value)
    {
        return $this->setData(self::ENABLE_SIGN_UP, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSignUpTemplate()
    {
        return $this->getData(self::SIGN_UP_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSignUpTemplate($value)
    {
        return $this->setData(self::SIGN_UP_TEMPLATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnableWithdrawRequest()
    {
        return $this->getData(self::ENABLE_WITHDRAW_REQUEST);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnableWithdrawRequest($value)
    {
        return $this->setData(self::ENABLE_WITHDRAW_REQUEST, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getWithdrawRequestTemplate()
    {
        return $this->getData(self::WITHDRAW_REQUEST_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setWithdrawRequestTemplate($value)
    {
        return $this->setData(self::WITHDRAW_REQUEST_TEMPLATE, $value);
    }
}
