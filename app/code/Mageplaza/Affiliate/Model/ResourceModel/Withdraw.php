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

namespace Mageplaza\Affiliate\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime;
use Mageplaza\Affiliate\Model\Withdraw\Status;

/**
 * Class Withdraw
 * @package Mageplaza\Affiliate\Model\ResourceModel
 */
class Withdraw extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_affiliate_withdraw', 'withdraw_id');
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            $object->setCreatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        }

        if ($object->dataHasChangedFor('status') && $object->getStatus() == Status::COMPLETE) {
            $object->setResolvedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        }

        return parent::_beforeSave($object);
    }
}
