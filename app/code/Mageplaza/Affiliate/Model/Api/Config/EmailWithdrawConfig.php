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

use Mageplaza\Affiliate\Api\Data\Config\EmailWithdrawConfigInterface;

/**
 * Class EmailWithdrawConfig
 * @package Mageplaza\Affiliate\Model\Api\Config
 */
class EmailWithdrawConfig extends \Magento\Framework\DataObject implements EmailWithdrawConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEnable()
    {
        return $this->getData(self::ENABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnable($value)
    {
        return $this->setData(self::ENABLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getComplete()
    {
        return $this->getData(self::COMPLETE);
    }

    /**
     * {@inheritdoc}
     */
    public function setComplete($value)
    {
        return $this->setData(self::COMPLETE, $value);
    }
}
