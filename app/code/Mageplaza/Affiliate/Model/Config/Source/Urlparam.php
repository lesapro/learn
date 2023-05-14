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
 * Class Urlparam
 * @package Mageplaza\Affiliate\Model\Config\Source
 */
class Urlparam implements ArrayInterface
{
    const PARAM_ID = 'account_id';
    const PARAM_CODE = 'code';

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
        return [
            self::PARAM_CODE => __('Affiliate Code'),
            self::PARAM_ID => __('Affiliate ID')
        ];
    }
}
