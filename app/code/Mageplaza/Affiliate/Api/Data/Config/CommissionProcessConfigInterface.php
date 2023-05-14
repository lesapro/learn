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
 * Interface CommissionProcessConfigInterface
 * @api
 */
interface CommissionProcessConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const EARN_COMMISSION_INVOICE = 'earn_commission_invoice';

    const HOLDING_DAYS = 'holding_days';

    const REFUND = 'refund';

    /**
     * @return bool
     */
    public function getEarnCommissionInvoice();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEarnCommissionInvoice($value);

    /**
     * @return int
     */
    public function getHoldingDays();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setHoldingDays($value);

    /**
     * @return bool
     */
    public function getRefund();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setRefund($value);
}
