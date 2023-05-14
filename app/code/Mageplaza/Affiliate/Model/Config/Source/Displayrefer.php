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
 * Class Displayrefer
 * @package Mageplaza\Affiliate\Model\Config\Source
 */
class Displayrefer implements ArrayInterface
{
    const CATEGORY_PAGE = 'list';
    const PRODUCT_PAGE = 'detail';

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
        return [self::CATEGORY_PAGE => __('Category page'), self::PRODUCT_PAGE => __('Product page')];
    }
}
