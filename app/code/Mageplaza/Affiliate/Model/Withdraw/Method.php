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

namespace Mageplaza\Affiliate\Model\Withdraw;

use Magento\Framework\Option\ArrayInterface;
use Mageplaza\Affiliate\Helper\Payment;
use Zend_Serializer_Exception;

/**
 * Class Method
 * @package Mageplaza\Affiliate\Model\Withdraw
 */
class Method implements ArrayInterface
{
    /**
     * @var Payment
     */
    protected $_paymentHelper;

    /**
     * Method constructor.
     *
     * @param Payment $helper
     */
    public function __construct(Payment $helper)
    {
        $this->_paymentHelper = $helper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * @return array
     * @throws Zend_Serializer_Exception
     */
    public function getOptionHash()
    {
        $options = [];
        $paymentMethods = $this->_paymentHelper->getActiveMethods();
        foreach ($paymentMethods as $key => $method) {
            $options[$key] = $method['label'];
        }

        return $options;
    }
}
