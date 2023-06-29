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
 * Interface EmailWithdrawConfigInterface
 * @api
 */
interface EmailWithdrawConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENABLE = 'enable';

    const COMPLETE = 'complete';

    /**
     * @return bool
     */
    public function getEnable();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnable($value);

    /**
     * @return string
     */
    public function getComplete();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setComplete($value);
}
