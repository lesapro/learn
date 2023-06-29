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
 * Interface AccountInterface
 * @api
 */
interface AccountInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ACCOUNT_ID = 'account_id';

    const CUSTOMER_ID = 'customer_id';

    const CODE = 'code';

    const GROUP_ID = 'group_id';

    const BALANCE = 'balance';

    const HOLDING_BALANCE = 'holding_balance';

    const TOTAL_COMMISSION = 'total_commission';

    const TOTAL_PAID = 'total_paid';

    const STATUS = 'status';

    const EMAIL_NOTIFICATION = 'email_notification';

    const PARENT = 'parent';

    const TREE = 'tree';

    const WITHDRAW_METHOD = 'withdraw_method';

    const WITHDRAW_INFORMATION = 'withdraw_information';

    const CREATED_AT = 'created_at';

    const PARENT_EMAIL = 'parent_email';

    /**#@-*/

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
     * @return string
     */
    public function getCode();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCode($value);

    /**
     * @return int
     */
    public function getGroupId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setGroupId($value);

    /**
     * @return float
     */
    public function getBalance();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setBalance($value);

    /**
     * @return float
     */
    public function getHoldingBalance();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setHoldingBalance($value);

    /**
     * @return float
     */
    public function getTotalCommission();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setTotalCommission($value);

    /**
     * @return float
     */
    public function getTotalPaid();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setTotalPaid($value);

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
     * @return int
     */
    public function getEmailNotification();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setEmailNotification($value);

    /**
     * @return int
     */
    public function getParent();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setParent($value);

    /**
     * @return string
     */
    public function getTree();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTree($value);

    /**
     * @return string
     */
    public function getWithdrawMethod();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setWithdrawMethod($value);

    /**
     * @return string
     */
    public function getWithdrawInformation();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setWithdrawInformation($value);

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
    public function getParentEmail();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setParentEmail($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Mageplaza\Affiliate\Api\Data\AccountExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Mageplaza\Affiliate\Api\Data\AccountExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(
        \Mageplaza\Affiliate\Api\Data\AccountExtensionInterface $extensionAttributes
    );
}
