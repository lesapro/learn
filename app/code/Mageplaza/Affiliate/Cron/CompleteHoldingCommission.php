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
 * @copyright   Copyright (c) 2018 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Affiliate\Cron;

use Exception;
use Mageplaza\Affiliate\Model\Transaction\Status;
use Psr\Log\LoggerInterface;
use Mageplaza\Affiliate\Helper\Data;
use Mageplaza\Affiliate\Model\TransactionFactory;

/**
 * Class CompleteHoldingCommission
 * @package Mageplaza\Affiliate\Cron
 */
class CompleteHoldingCommission
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @param LoggerInterface $logger
     * @param Data $dataHelper
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(
        LoggerInterface $logger,
        Data $dataHelper,
        TransactionFactory $transactionFactory
    ) {
        $this->logger              = $logger;
        $this->_dataHelper         = $dataHelper;
        $this->_transactionFactory = $transactionFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (!$this->_dataHelper->isEnabled()) {
            return;
        }

        $transactionModel = $this->_transactionFactory->create()->getCollection()
            ->addFieldToFilter('status', Status::STATUS_HOLD);

        $transactionModel->getSelect()->where('`holding_to` <= NOW()');

        /** @var \Mageplaza\Affiliate\Model\Transaction $transaction */
        foreach ($transactionModel as $transaction) {
            try {
                $transaction->complete();
            } catch (Exception $e) {
                $this->logger->debug($e->getMessage());
            }
        }
    }
}
