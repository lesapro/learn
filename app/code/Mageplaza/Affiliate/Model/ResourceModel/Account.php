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
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Mageplaza\Affiliate\Helper\Data;
use Mageplaza\Affiliate\Model\Account\Status;

/**
 * Class Account
 * @package Mageplaza\Affiliate\Model\ResourceModel
 */
class Account extends AbstractDb
{
    /**
     * @type Data
     */
    protected $_helper;

    /**
     * Account constructor.
     *
     * @param Data $helper
     * @param Context $context
     */
    public function __construct(
        Data $helper,
        Context $context
    ) {
        $this->_helper = $helper;

        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_affiliate_account', 'account_id');
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        if ($object->isObjectNew()) {
            $object->setCreatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
            if (!$object->hasData('group_id')) {
                $object->setGroupId($this->_helper->getDefaultGroup());
            }
        }
        if (!$object->hasData('status')) {
            $status = $this->_helper->isAdminApproved() ? Status::NEED_APPROVED : Status::ACTIVE;
            $object->setStatus($status);
        }
        if (!$object->hasData('code')) {
            $object->setCode($this->generateAffiliateCode());
        }

        return parent::_beforeSave($object);
    }

    /**
     * @inheritdoc
     */
    protected function _afterSave(AbstractModel $object)
    {
        parent::_afterSave($object);
        if ($object->isObjectNew()) {
            $account = $this->_helper->getAffiliateAccount($object->getId());
            if ($parentId = $object->getParent()) {
                $parent = $this->_helper->getAffiliateAccount($parentId);
                if ($parent && $parent->getId()) {
                    $account->setTree($parent->getTree() . '/' . $account->getId())
                        ->save();
                }
            } else {
                $account->setTree($account->getId())
                    ->save();
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function generateAffiliateCode()
    {
        $flag = true;
        do {
            $code    = substr(str_shuffle(hash('md5', microtime())), 0, $this->_helper->getCodeLength());
            $account = $this->_helper->getAffiliateAccount($code, 'code');
            if (!$account->getId()) {
                $flag = false;
            }
        } while ($flag);

        return $code;
    }
}
