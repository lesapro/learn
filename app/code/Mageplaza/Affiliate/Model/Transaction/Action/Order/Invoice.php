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

namespace Mageplaza\Affiliate\Model\Transaction\Action\Order;

use Magento\Framework\Phrase;
use Magento\Sales\Model\Order;
use Mageplaza\Affiliate\Model\Transaction\AbstractAction;
use Mageplaza\Affiliate\Model\Transaction\Status;
use Mageplaza\Affiliate\Model\Transaction\Type;

/**
 * Class Invoice
 *
 * @package Mageplaza\Affiliate\Model\Transaction\Action\Order
 */
class Invoice extends AbstractAction
{
    /**
     * @return mixed
     */
    public function getAmount()
    {
        $object = $this->getObject();
        $amount = $object->getCommissionAmount();

        if ($object instanceof Order) {
            $amount -= $this->transactionFactory->create()
                ->getCollection()
                ->addFieldToFilter('account_id', $this->getAccount()->getId())
                ->addFieldToFilter('action', 'order/invoice')
                ->addFieldToFilter('order_id', $object->getId())
                ->getFieldTotal();
        }

        return max(0, $amount);
    }

    /**
     * @return int
     */
    public function getType()
    {
        return Type::COMMISSION;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        $holdDays = $this->getHoldDays();
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
        $param = $transaction === null
            ? '#' . $this->getOrder()->getIncrementId()
            : '#' . $transaction->getOrderIncrementId();

        return __('Get commission for order %1', $param);
    }

    /**
     * @return array
     */
    public function prepareAction()
    {
        $order           = $this->getOrder();
        $transactionData = [
            'order_id'           => $order->getId(),
            'order_increment_id' => $order->getIncrementId(),
            'store_id'           => $order->getStoreId(),
            'campaign_id'        => $order->getAffiliateCampaigns()
        ];

        $holdDays = $this->getHoldDays();
        if ($holdDays > 0) {
            $transactionData['holding_to'] = $this->getHoldingDate($holdDays);
        }

        return $transactionData;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        $object = $this->getObject();
        if ($object instanceof Order\Invoice) {
            $order = $object->getOrder();
        } else {
            $order = $object;
        }

        return $order;
    }

    /**
     * @return string
     */
    public function getAdditionContent()
    {
        $extraContent = $this->getExtraContent();
        $object       = $this->getObject();
        if ($object instanceof Order\Invoice) {
            $extraContent['invoice_increment_id'] = $object->getIncrementId();
        }

        return $this->jsonHelper->jsonEncode($extraContent);
    }
}
