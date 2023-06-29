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

use Mageplaza\Affiliate\Api\Data\Config\ReferSharingContentInterface;

/**
 * Class ReferSharingContentConfig
 * @package Mageplaza\Affiliate\Model\Api\Config
 */
class ReferSharingContentConfig extends \Magento\Framework\DataObject implements ReferSharingContentInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->getData(self::SUBJECT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject($value)
    {
        return $this->setData(self::SUBJECT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailContent()
    {
        return $this->getData(self::EMAIL_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailContent($value)
    {
        return $this->setData(self::EMAIL_CONTENT, $value);
    }
}
