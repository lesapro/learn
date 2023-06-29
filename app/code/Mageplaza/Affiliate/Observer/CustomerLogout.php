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

namespace Mageplaza\Affiliate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Mageplaza\Affiliate\Helper\Data;

/**
 * Class CustomerLogout
 * @package Mageplaza\Affiliate\Observer
 */
class CustomerLogout implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * CustomerLogout constructor.
     *
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @return $this|void
     * @throws FailureToSendException
     * @throws InputException
     */
    public function execute(Observer $observer)
    {
        $affCode = $this->_helper->getAffiliateKeyFromCookie();
        $affSource = $this->_helper->getAffiliateKeyFromCookie(Data::AFFILIATE_COOKIE_SOURCE_NAME);
        if ($affCode && $affSource === 'coupon') {
            $this->_helper->deleteAffiliateCookieSourceName();
        }

        return $this;
    }
}
