<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\AutoLanguageSwitcher\Plugin\Store\Model;

use Magefan\AutoLanguageSwitcher\Model\Config;

/**
 * Class StorePlugin
 * @package Magefan\AutoLanguageSwitcher\Plugin\Store\Model
 */
class StorePlugin
{
    /**
     * StorePlugin constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param \Magento\Store\Model\Store $subject
     * @param bool $fromStore
     * @return array
     */
    public function beforeGetCurrentUrl(
        \Magento\Store\Model\Store $subject,
        $fromStore = true
    ) {
        if (false && $this->config->isEnabled()) {
            return [false];
        }
    }
}
