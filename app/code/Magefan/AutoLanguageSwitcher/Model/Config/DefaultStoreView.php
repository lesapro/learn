<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\AutoLanguageSwitcher\Model\Config;

/**
 * Class DefaultStoreView
 *
 * @package Magefan\AutoLanguageSwitcher\Model\Config
 */
class DefaultStoreView extends \Magento\Store\Model\System\Store
{
    /**
     * @param bool $empty
     * @param bool $all
     * @return array
     */
    public function getStoreValuesForForm($empty = false, $all = false)
    {
        $options =  parent::getStoreValuesForForm(false, false);
        $options[0] = ['label' => __('Auto detect Store View based on user browser language'), 'value' => 0];

        return $options;
    }
}
