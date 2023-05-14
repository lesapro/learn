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
 * Interface CommissionConfigInterface
 * @api
 */
interface CommissionConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const BY_TAX = 'by_tax';

    const SHIPPING = 'shipping';

    const PROCESS = 'process';

    /**
     * @return bool
     */
    public function getByTax();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setByTax($value);

    /**
     * @return bool
     */
    public function getShipping();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setShipping($value);

    /**
     * @return \Mageplaza\Affiliate\Api\Data\Config\CommissionProcessConfigInterface
     */
    public function getProcess();

    /**
     * @param \Mageplaza\Affiliate\Api\Data\Config\CommissionProcessConfigInterface $value
     *
     * @return $this
     */
    public function setProcess(\Mageplaza\Affiliate\Api\Data\Config\CommissionProcessConfigInterface $value);
}
