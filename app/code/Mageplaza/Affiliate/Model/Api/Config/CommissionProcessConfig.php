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

use Mageplaza\Affiliate\Api\Data\Config\CommissionProcessConfigInterface;

/**
 * Class CommissionProcessConfig
 * @package Mageplaza\Affiliate\Model\Api\Config
 */
class CommissionProcessConfig extends \Magento\Framework\DataObject implements CommissionProcessConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEarnCommissionInvoice()
    {
        return $this->getData(self::EARN_COMMISSION_INVOICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEarnCommissionInvoice($value)
    {
        return $this->setData(self::EARN_COMMISSION_INVOICE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getHoldingDays()
    {
        return $this->getData(self::HOLDING_DAYS);
    }

    /**
     * {@inheritdoc}
     */
    public function setHoldingDays($value)
    {
        return $this->setData(self::HOLDING_DAYS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRefund()
    {
        return $this->getData(self::REFUND);
    }

    /**
     * {@inheritdoc}
     */
    public function setRefund($value)
    {
        return $this->setData(self::REFUND, $value);
    }
}
