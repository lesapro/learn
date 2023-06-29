<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\AutoLanguageSwitcher\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magefan\AutoLanguageSwitcher\Model\Config;

class SuggestedStore extends Template
{
    /**
     * @var Config
     */
    public $config;


    /**
     * SuggestedStore constructor.
     * @param Context $context
     * @param Config $config
     */
    public function __construct(
        Context $context,
        Config $config
    ) {
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * @return false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig()
    {
        return json_encode([
            'api_url'       => $this->_storeManager->getStore()->getBaseUrl(),
            'current_url' => base64_encode($this->_storeManager->getStore()->getCurrentUrl(false))
        ]);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if ($this->config->isEnabled()
            && $this->config->isSwitcherPopupEnabled()
            && $this->config->isAllowedOnPage()
        ) {
            return parent::toHtml();
        }
        return '';
    }
}
