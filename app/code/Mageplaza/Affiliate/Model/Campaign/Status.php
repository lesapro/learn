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
 * Class Status
 * @package Mageplaza\Affiliate\Model\Campaign
 */
class Status implements ArrayInterface
{
    const ENABLED = 1;
    const DISABLED = 2;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::ENABLED,
                'label' => __('Enabled')
            ],
            [
                'value' => self::DISABLED,
                'label' => __('Disabled')
            ],
        ];

        return $options;
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {
        return [
            self::ENABLED => __('Enabled'),
            self::DISABLED => __('Disabled')
        ];
    }
}
