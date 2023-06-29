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

namespace Mageplaza\Affiliate\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Urltype
 * @package Mageplaza\Affiliate\Model\Config\Source
 */
class Urltype implements ArrayInterface
{
    const TYPE_HASH = 'hash';
    const TYPE_PARAM = 'param';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $array = [];
        foreach ($this->getOptionHash() as $key => $label) {
            $array[] = [
                'value' => $key,
                'label' => $label
            ];
        }

        return $array;
    }

    /**
     * @return array
     */
    public function getOptionHash()
    {
        $array = [
            self::TYPE_HASH => __('Hash'),
            self::TYPE_PARAM => __('Parameter')
        ];

        return $array;
    }
}
