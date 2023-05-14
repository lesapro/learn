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

namespace Mageplaza\Affiliate\Model\Campaign;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Discount
 * @package Mageplaza\Affiliate\Model\Campaign
 */
class Discount implements ArrayInterface
{
    const PERCENT = 'by_percent';
    const FIXED = 'by_fixed';
    const CART_FIXED = 'cart_fixed';
    const BUY_X_GET_Y = 'buy_x_get_y';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::PERCENT,
                'label' => __('Percent of cart total')
            ],
            [
                'value' => self::CART_FIXED,
                'label' => __('Fixed amount discount for whole cart')
            ],
        ];

        return $options;
    }
}
