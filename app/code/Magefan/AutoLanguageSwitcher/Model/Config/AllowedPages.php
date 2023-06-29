<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\AutoLanguageSwitcher\Model\Config;

/**
 * Class AllowedPages
 * @package Magefan\AutoLanguageSwitcher\Model\Config
 */
class AllowedPages implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return    [
            ['label' => __('All pages'),                       'value' => 0],
            ['label' => __('Specific pages'),                  'value' => 1],
            ['label' => __('All pages except specific pages'), 'value' => 2]
        ];
    }
}
