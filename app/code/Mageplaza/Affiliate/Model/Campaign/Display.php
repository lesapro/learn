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
 * Class Display
 * @package Mageplaza\Affiliate\Model\Campaign
 */
class Display implements ArrayInterface
{
    const ALLOW_GUEST = 1;
    const AFFILIATE_MEMBER_ONLY = 2;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::ALLOW_GUEST,
                'label' => __('Allow Guest')
            ],
            [
                'value' => self::AFFILIATE_MEMBER_ONLY,
                'label' => __('Affiliate Member Only')
            ],
        ];

        return $options;
    }
}
