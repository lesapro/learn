<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\AutoLanguageSwitcher\Model\Config;

/**
 * Class SwitcherMode
 * @package Magefan\AutoLanguageSwitcher\Model\Config
 */
class SwitcherMode implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return    [
            ['label' => __('Auto Redirect'),     'value' => 0],
            ['label' => __('Suggestion Popup'), 'value' => 1]
        ];
    }
}
