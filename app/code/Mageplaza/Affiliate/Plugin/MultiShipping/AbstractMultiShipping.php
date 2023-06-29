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

namespace Mageplaza\Affiliate\Plugin\MultiShipping;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;

/**
 * Class AbstractMultiShipping
 * @package Mageplaza\Affiliate\Plugin\MultiShipping
 */
abstract class AbstractMultiShipping
{
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @param Cart $cart
     */
    public function __construct(
        Cart $cart
    ) {
        $this->cart = $cart;
    }

    /**
     * @param Action $subject
     */
    public function beforeExecute(Action $subject)
    {
        $quote = $this->cart->getQuote();
        if ($quote->getIsMultiShipping()) {
            $quote->setIsMultiShipping(0);
            $extensionAttributes = $quote->getExtensionAttributes();
            if ($extensionAttributes && $extensionAttributes->getShippingAssignments()) {
                $extensionAttributes->setShippingAssignments([]);
            }
            $this->cart->saveQuote();
        }
    }
}
