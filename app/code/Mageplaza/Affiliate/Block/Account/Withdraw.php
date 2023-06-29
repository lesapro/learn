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

namespace Mageplaza\Affiliate\Block\Account;

use LogicException;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\DataObject;
use Mageplaza\Affiliate\Block\Account;
use Zend_Serializer_Exception;

/**
 * Class Withdraw
 * @package Mageplaza\Affiliate\Block\Account
 */
class Withdraw extends Account
{
    /**
     * @var array
     */
    private $_formData;

    /**
     * @param string $field
     *
     * @return string|array
     */
    public function getFormData($field = '')
    {
        if ($field) {
            return isset($this->_formData[$field]) ? $this->_formData[$field] : '';
        }

        return $this->_formData;
    }

    /**
     * @param $data
     */
    public function setFormData($data)
    {
        $this->_formData = $data;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('My Withdrawal'));

        parent::_prepareLayout();

        if (!self::getFormData()) {
            $_formData = new DataObject();

            $postedData = $this->customerSession->getWithdrawFormData(true);
            if ($postedData) {
                $_formData->addData($postedData);
            }

            self::setFormData($_formData);
        }
    }

    /**
     * @return string
     */
    public function getWithdrawPostUrl()
    {
        return $this->getUrl('*/*/withdrawpost');
    }

    /**
     * @return bool
     * @throws Zend_Serializer_Exception
     */
    public function isAllowWithdraw()
    {
        return sizeof($this->getMethods()) && $this->_affiliateHelper->isAllowWithdrawRequest();
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function convertPrice($value)
    {
        return $this->objectManager->create('Magento\Directory\Model\PriceCurrency')->convert($value);
    }

    /**
     * Get tax fee withdraw
     * @return string
     * @throws Zend_Serializer_Exception
     */
    public function getFeeConfig()
    {
        $config = [];

        $paymentConfig = $this->getMethods();
        foreach ($paymentConfig as $code => $payment) {
            $fee = isset($payment['fee']) ? $payment['fee'] : 0;
            if (strpos($fee, '%') !== false) {
                $type = 'percent';
                $fee = floatval(trim($fee, '%'));
                if ($fee <= 0) {
                    continue;
                }
            } else {
                $type = 'fix';
                if (floatval($fee) <= 0) {
                    continue;
                }
                $fee = $this->convertPrice(floatval($fee));
            }

            $config[$code] = [
                'type' => $type,
                'value' => $fee
            ];
        }

        return $this->jsonHelper->jsonEncode($config);
    }

    /**
     * @param $code
     *
     * @return mixed
     * @throws Zend_Serializer_Exception
     */
    public function getMethodHtml($code)
    {
        $method = $this->paymentHelper->getMethodModel($code);

        return $method->getMethodHtml();
    }

    /**
     * @return array
     */
    public function getWithdrawPolicy()
    {
        $policy = [];
        if ($minBalance = $this->_affiliateHelper->getWithdrawMinimumBalance()) {
            $policy[] = __(
                'You can request withdraw when your balance equal or greater than %1',
                $this->formatPrice($minBalance)
            );
        }

        if ($min = $this->_affiliateHelper->getWithdrawMinimum()) {
            $policy[] = __('You can withdraw a minimum %1', $this->formatPrice($min));
        }

        if ($max = $this->_affiliateHelper->getWithdrawMaximum()) {
            $policy[] = __('You can withdraw a maximum %1', $this->formatPrice($max));
        }

        return $policy;
    }

    /**
     * @return mixed|null
     * @throws Zend_Serializer_Exception
     */
    public function getMethods()
    {
        return $this->paymentHelper->getActiveMethods();
    }

    /**
     * Get helper singleton
     *
     * @param string $className
     * @return AbstractHelper
     * @throws LogicException
     */
    public function helper($className)
    {
        $helper = $this->objectManager->get($className);
        if (($helper instanceof AbstractHelper) === false) {
            throw new LogicException($className . ' doesn\'t extends Magento\Framework\App\Helper\AbstractHelper');
        }

        return $helper;
    }
}
