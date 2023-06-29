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

namespace Mageplaza\Affiliate\Model\Api;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Api\ExtensionAttributesInterface;
use Mageplaza\Affiliate\Api\Data\WithdrawInterface;
use Mageplaza\Affiliate\Api\Data\WithdrawExtensionInterface;

/**
 * Class Withdraw
 * @package Mageplaza\Affiliate\Model
 */
class Withdraw extends AbstractExtensibleModel implements
    WithdrawInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\Affiliate\Model\ResourceModel\Withdraw');
    }

    /**
     * {@inheritdoc}
     */
    public function getWithdrawId()
    {
        return $this->getData(self::WITHDRAW_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setWithdrawId($value)
    {
        return $this->setData(self::WITHDRAW_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccountId()
    {
        return $this->getData(self::ACCOUNT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountId($value)
    {
        return $this->setData(self::ACCOUNT_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($value)
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRANSACTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTransactionId($value)
    {
        return $this->setData(self::TRANSACTION_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($value)
    {
        return $this->setData(self::AMOUNT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFee()
    {
        return $this->getData(self::FEE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFee($value)
    {
        return $this->setData(self::FEE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransferAmount()
    {
        return $this->getData(self::TRANSFER_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setTransferAmount($value)
    {
        return $this->setData(self::TRANSFER_AMOUNT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
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
    public function getPaymentDetails()
    {
        return $this->getData(self::PAYMENT_DETAILS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentDetails($value)
    {
        return $this->setData(self::PAYMENT_DETAILS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getResolvedAt()
    {
        return $this->getData(self::RESOLVED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setResolvedAt($value)
    {
        return $this->setData(self::RESOLVED_AT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getWithdrawDescription()
    {
        return $this->getData(self::WITHDRAW_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setWithdrawDescription($value)
    {
        return $this->setData(self::WITHDRAW_DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getOfflineAddress()
    {
        return $this->getData(self::OFFLINE_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setOfflineAddress($value)
    {
        return $this->setData(self::OFFLINE_ADDRESS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBanktranfer()
    {
        return $this->getData(self::BANKTRANFER);
    }

    /**
     * {@inheritdoc}
     */
    public function setBanktranfer($value)
    {
        return $this->setData(self::BANKTRANFER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaypalEmail()
    {
        return $this->getData(self::PAYPAL_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaypalEmail($value)
    {
        return $this->setData(self::PAYPAL_EMAIL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaypalTransactionId()
    {
        return $this->getData(self::PAYPAL_TRANSACTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaypalTransactionId($value)
    {
        return $this->setData(self::PAYPAL_TRANSACTION_ID, $value);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return ExtensionAttributesInterface
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param WithdrawExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(
        WithdrawExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
