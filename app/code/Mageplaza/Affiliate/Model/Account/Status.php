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

namespace Mageplaza\Affiliate\Model\Account;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 * @package Mageplaza\Affiliate\Model\Account
 */
class Status implements ArrayInterface
{
    const ACTIVE = 1;
    const INACTIVE = 2;
    const NEED_APPROVED = 3;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::ACTIVE,
                'label' => __('Active')
            ],
            [
                'value' => self::INACTIVE,
                'label' => __('Inactive')
            ],
            [
                'value' => self::NEED_APPROVED,
                'label' => __('Need Approved')
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
            self::ACTIVE => __('Active'),
            self::INACTIVE => __('Inactive'),
            self::NEED_APPROVED => __('Need Approved')
        ];
    }
}
