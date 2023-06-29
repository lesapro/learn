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
 * Interface WithdrawConfigInterface
 * @api
 */
interface WithdrawConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ALLOW_REQUEST = 'allow_request';

    const PAYMENT_METHOD = 'payment_method';

    const MINIMUM_BALANCE = 'minimum_balance';

    const MINIMUM = 'minimum';

    const MAXIMUM = 'maximum';

    /**
     * @return bool
     */
    public function getAllowRequest();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setAllowRequest($value);

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
     * @return float
     */
    public function getMinimumBalance();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setMinimumBalance($value);

    /**
     * @return float
     */
    public function getMinimum();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setMinimum($value);

    /**
     * @return float
     */
    public function getMaximum();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setMaximum($value);
}
