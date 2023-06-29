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

namespace Mageplaza\Affiliate\Model\Transaction\Action;

use Magento\Framework\Phrase;
use Mageplaza\Affiliate\Model\Transaction\AbstractAction;
use Mageplaza\Affiliate\Model\Transaction\Status;
use Mageplaza\Affiliate\Model\Transaction\Type;

/**
 * Class Admin
 * @package Mageplaza\Affiliate\Model\Transaction\Action
 */
class Admin extends AbstractAction
{
    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->dataHelper->getPriceCurrency()->round($this->getObject()->getAmount());
    }

    /**
     * @return int
     */
    public function getType()
    {
        return Type::ADMIN;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        $holdDays = $this->getObject()->getHoldDay();
        if ($holdDays && $holdDays > 0) {
            return Status::STATUS_HOLD;
        }

        return Status::STATUS_COMPLETED;
    }

    /**
     * @param null $transaction
     *
     * @return Phrase
     */
    public function getTitle($transaction = null)
    {
        $object = $transaction === null ? $this->getObject() : $transaction;

        return $object->getTitle() ?: __('Changed by Admin');
    }

    /**
     * @return array
     */
    public function prepareAction()
    {
        $actionObject = $this->getObject();
        if ($holdDay = $actionObject->getHoldDay()) {
            return ['holding_to' => $this->getHoldingDate($holdDay)];
        }

        return [];
    }
}
