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
 * Class Showlink
 * @package Mageplaza\Affiliate\Model\Config\Source
 */
class Showlink implements ArrayInterface
{
    const SHOW_ON_TOP_LINK = 'top_link';
    const SHOW_ON_FOOTER_LINK = 'footer_link';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $optionArray[] = ['value' => '', 'label' => __('-- Please Select --')];

        foreach ($this->toArray() as $key => $value) {
            $optionArray[] = ['value' => $key, 'label' => $value];
        }

        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::SHOW_ON_FOOTER_LINK => __('Footer Link'), self::SHOW_ON_TOP_LINK => __('Top Link')];
    }
}
