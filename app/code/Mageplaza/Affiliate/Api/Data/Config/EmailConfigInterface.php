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

namespace Mageplaza\Affiliate\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface EmailConfigInterface
 * @api
 */
interface EmailConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const SENDER = 'sender';

    const ADMIN = 'admin';

    const ACCOUNT = 'account';

    const TRANSACTION = 'transaction';

    const WITHDRAW = 'withdraw';

    /**
     * @return string
     */
    public function getSender();

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setSender($value);

    /**
     * @return \Mageplaza\Affiliate\Api\Data\Config\EmailAdminConfigInterface
     */
    public function getAdmin();

    /**
     * @param \Mageplaza\Affiliate\Api\Data\Config\EmailAdminConfigInterface $emailAdminConfig
     *
     * @return $this
     */
    public function setAdmin(\Mageplaza\Affiliate\Api\Data\Config\EmailAdminConfigInterface $emailAdminConfig);

    /**
     * @return \Mageplaza\Affiliate\Api\Data\Config\EmailAccountConfigInterface
     */
    public function getAccount();

    /**
     * @param \Mageplaza\Affiliate\Api\Data\Config\EmailAccountConfigInterface $emailAccountConfig
     *
     * @return $this
     */
    public function setAccount(\Mageplaza\Affiliate\Api\Data\Config\EmailAccountConfigInterface $emailAccountConfig);

    /**
     * @return \Mageplaza\Affiliate\Api\Data\Config\EmailTransactionConfigInterface
     */
    public function getTransaction();

    /**
     * @param \Mageplaza\Affiliate\Api\Data\Config\EmailTransactionConfigInterface $emailTransactionConfig
     *
     * @return $this
     */
    public function setTransaction(\Mageplaza\Affiliate\Api\Data\Config\EmailTransactionConfigInterface $emailTransactionConfig);

    /**
     * @return \Mageplaza\Affiliate\Api\Data\Config\EmailWithdrawConfigInterface
     */
    public function getWithdraw();

    /**
     * @param \Mageplaza\Affiliate\Api\Data\Config\EmailWithdrawConfigInterface $emailWithdrawConfig
     *
     * @return $this
     */
    public function setWithdraw(\Mageplaza\Affiliate\Api\Data\Config\EmailWithdrawConfigInterface $emailWithdrawConfig);
}
