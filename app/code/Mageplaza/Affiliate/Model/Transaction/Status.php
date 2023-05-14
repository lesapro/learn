<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
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

namespace Mageplaza\Affiliate\Model\Transaction;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 * @package Mageplaza\Affiliate\Model\Transaction
 */
class Status implements ArrayInterface
{
    const STATUS_HOLD = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getOptionHash()
    {
        return [
            self::STATUS_HOLD => __('On Hold'),
            self::STATUS_COMPLETED => __('Completed'),
            self::STATUS_CANCELED => __('Cancelled')
        ];
    }
}
