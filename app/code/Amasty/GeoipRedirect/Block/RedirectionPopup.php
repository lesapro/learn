<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GeoipRedirect
 */


namespace Amasty\GeoipRedirect\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Amasty\GeoipRedirect\Model\Source\PopupType;

class RedirectionPopup extends Template
{
    protected $_template = 'popup.phtml';

    public function getType()
    {
        $websiteId = $this->_storeManager->getWebsite()->getId();
        $popupType = $this->_scopeConfig->getValue('amgeoipredirect/general/decline_redirection_type',
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $popupType;
    }
    public function getText()
    {
        $popupType = $this->getType();
        $storeId = $this->_storeManager->getStore()->getId();

        if ($popupType == PopupType::NOTIFICATION) {
            $popupText = $this->_scopeConfig->getValue('amgeoipredirect/general/decline_redirection_notification_text',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        } else {
            $popupText = $this->_scopeConfig->getValue('amgeoipredirect/general/decline_redirection_confirmation_text',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        return $popupText;
    }

    protected function _toHtml()
    {
        if ((bool)$this->getNeedShow()) {
            return parent::_toHtml();
        }

        return '';
    }

    public function getNeedShow()
    {
        if (!$this->_session->isSessionExists()) {
            $this->_session->start();
        }
        return $this->_session->getNeedShow();
    }
}
