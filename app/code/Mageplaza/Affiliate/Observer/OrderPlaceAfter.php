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
use Mageplaza\Affiliate\Helper\Data as AffiliateHelper;

/**
 * Class OrderPlaceAfter
 * @package Mageplaza\Affiliate\Observer
 */
class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var AffiliateHelper
     */
    protected $_helper;

    /**
     * OrderPlaceAfter constructor.
     *
     * @param AffiliateHelper $helper
     */
    public function __construct(
        AffiliateHelper $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @return mixed
     * @throws InputException
     * @throws FailureToSendException
     */
    public function execute(Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }
        $order = $observer->getEvent()->getOrder();
        if ($order->getAffiliateKey()) {
            $this->_helper->deleteAffiliateCookieSourceName();
        }

        return $this;
    }
}
