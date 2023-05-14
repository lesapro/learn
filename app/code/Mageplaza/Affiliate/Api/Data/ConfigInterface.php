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
 * Interface ConfigInterface
 * @api
 */
interface ConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const GENERAL = 'general';

    const ACCOUNT = 'account';

    const COMMISSION = 'commission';

    const WITHDRAW = 'withdraw';

    const EMAIL = 'email';

    const REFER = 'refer';

    /**
     * @return \Mageplaza\Affiliate\Api\Data\Config\GeneralConfigInterface
     */
    public function getGeneral();

    /**
     * @param \Mageplaza\Affiliate\Api\Data\Config\GeneralConfigInterface $generalConfig
     *
     * @return mixed
     */
    public function setGeneral(\Mageplaza\Affiliate\Api\Data\Config\GeneralConfigInterface $generalConfig);

    /**
     * @return \Mageplaza\Affiliate\Api\Data\Config\AccountConfigInterface
     */
    public function getAccount();

    /**
     * @param \Mageplaza\Affiliate\Api\Data\Config\AccountConfigInterface $accountConfig
     *
     * @return mixed
     */
    public function setAccount(\Mageplaza\Affiliate\Api\Data\Config\AccountConfigInterface $accountConfig);

    /**
     * @return \Mageplaza\Affiliate\Api\Data\Config\CommissionConfigInterface
     */
    public function getCommission();

    /**
     * @param \Mageplaza\Affiliate\Api\Data\Config\CommissionConfigInterface $commissionConfig
     *
     * @return mixed
     */
    public function setCommission(\Mageplaza\Affiliate\Api\Data\Config\CommissionConfigInterface $commissionConfig);

    /**
     * @return \Mageplaza\Affiliate\Api\Data\Config\WithdrawConfigInterface
     */
    public function getWithdraw();

    /**
     * @param \Mageplaza\Affiliate\Api\Data\Config\WithdrawConfigInterface $withdrawConfig
     *
     * @return mixed
     */
    public function setWithdraw(\Mageplaza\Affiliate\Api\Data\Config\WithdrawConfigInterface $withdrawConfig);

    /**
     * @return \Mageplaza\Affiliate\Api\Data\Config\EmailConfigInterface
     */
    public function getEmail();

    /**
     * @param \Mageplaza\Affiliate\Api\Data\Config\EmailConfigInterface $emailConfig
     *
     * @return mixed
     */
    public function setEmail(\Mageplaza\Affiliate\Api\Data\Config\EmailConfigInterface $emailConfig);

    /**
     * @return \Mageplaza\Affiliate\Api\Data\Config\ReferConfigInterface
     */
    public function getRefer();

    /**
     * @param \Mageplaza\Affiliate\Api\Data\Config\ReferConfigInterface $referConfig
     *
     * @return mixed
     */
    public function setRefer(\Mageplaza\Affiliate\Api\Data\Config\ReferConfigInterface $referConfig);
}
