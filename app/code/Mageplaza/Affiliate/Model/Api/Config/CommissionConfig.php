<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\Affiliate\Model\Api\Config;

use Mageplaza\Affiliate\Api\Data\Config\CommissionConfigInterface;

/**
 * Class CommissionConfig
 * @package Mageplaza\Affiliate\Model\Api\Config
 */
class CommissionConfig extends \Magento\Framework\DataObject implements CommissionConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByTax()
    {
        return $this->getData(self::BY_TAX);
    }

    /**
     * {@inheritdoc}
     */
    public function setByTax($value)
    {
        return $this->setData(self::BY_TAX, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getShipping()
    {
        return $this->getData(self::SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    public function setShipping($value)
    {
        return $this->setData(self::SHIPPING, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getProcess()
    {
        return $this->getData(self::PROCESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setProcess($value)
    {
        return $this->setData(self::PROCESS, $value);
    }
}
