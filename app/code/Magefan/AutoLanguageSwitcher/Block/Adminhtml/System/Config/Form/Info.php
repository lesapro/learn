<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\AutoLanguageSwitcher\Block\Adminhtml\System\Config\Form;

/**
 * Admin Magefan configurations information block
 */
class Info extends \Magefan\Community\Block\Adminhtml\System\Config\Form\Info
{
    /**
     * Return extension url
     * @return string
     */
    protected function getModuleUrl()
    {
        return 'https://magefan.com/magento-2-auto-language-switcher-multi-language-store';
    }

    /**
     * Return extension title
     * @return string
     */
    protected function getModuleTitle()
    {
        return 'Auto Language Switcher Extension';
    }

    /**
     * Return info block html
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = parent::render($element);
        $html .= '<div style="padding:10px;background-color:#ffe5e5;border:1px solid #ddd;margin-bottom:7px;">
            <strong>Attention!</strong> Once changes being made, please make sure that you have flushed browser cookie or use anonymous-browser tab for testing.
        </div>';

        return $html;
    }
}
