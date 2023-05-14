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

namespace Mageplaza\Affiliate\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface WithdrawInterface
 * @api
 */
interface WithdrawInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const WITHDRAW_ID = 'withdraw_id';

    const ACCOUNT_ID = 'account_id';

    const CUSTOMER_ID = 'customer_id';

    const TRANSACTION_ID = 'transaction_id';

    const AMOUNT = 'amount';

    const FEE = 'fee';

    const TRANSFER_AMOUNT = 'transfer_amount';

    const STATUS = 'status';

    const PAYMENT_METHOD = 'payment_method';

    const PAYMENT_DETAILS = 'payment_details';

    const RESOLVED_AT = 'resolved_at';

    const CREATED_AT = 'created_at';

    const WITHDRAW_DESCRIPTION = 'withdraw_description';

    const OFFLINE_ADDRESS = 'offline_address';

    const BANKTRANFER = 'banktranfer';

    const PAYPAL_EMAIL = 'paypal_email';

    const PAYPAL_TRANSACTION_ID = 'paypal_transaction_id';

    /**#@-*/

    /**
     * @return int
     */
    public function getWithdrawId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setWithdrawId($value);

    /**
     * @return int
     */
    public function getAccountId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setAccountId($value);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCustomerId($value);

    /**
     * @return int
     */
    public function getTransactionId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setTransactionId($value);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setAmount($value);

    /**
     * @return float
     */
    public function getFee();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setFee($value);

    /**
     * @return float
     */
    public function getTransferAmount();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setTransferAmount($value);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getPaymentMethod();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPaymentMethod($value);

    /**
     * @return string
     */
    public function getPaymentDetails();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPaymentDetails($value);

    /**
     * @return string
     */
    public function getResolvedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setResolvedAt($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getWithdrawDescription();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setWithdrawDescription($value);

    /**
     * @return string|null
     */
    public function getOfflineAddress();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOfflineAddress($value);

    /**
     * @return string|null
     */
    public function getBanktranfer();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setBanktranfer($value);

    /**
     * @return string|null
     */
    public function getPaypalEmail();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPaypalEmail($value);

    /**
     * @return string|null
     */
    public function getPaypalTransactionId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPaypalTransactionId($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Mageplaza\Affiliate\Api\Data\WithdrawExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Mageplaza\Affiliate\Api\Data\WithdrawExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(
        \Mageplaza\Affiliate\Api\Data\WithdrawExtensionInterface $extensionAttributes
    );
}
