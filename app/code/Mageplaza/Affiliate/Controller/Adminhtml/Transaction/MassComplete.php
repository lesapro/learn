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

namespace Mageplaza\Affiliate\Controller\Adminhtml\Transaction;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\Affiliate\Model\ResourceModel\Transaction\CollectionFactory;

/**
 * Class MassComplete
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Transaction
 */
class MassComplete extends Action
{
    /**
     * @var CollectionFactory
     */
    protected $_transactionFactory;

    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * MassComplete constructor.
     *
     * @param CollectionFactory $transactionFactory
     * @param Context $context
     * @param Filter $filter
     */
    public function __construct(
        CollectionFactory $transactionFactory,
        Context $context,
        Filter $filter
    ) {
        $this->_transactionFactory = $transactionFactory;
        $this->_filter = $filter;
        parent::__construct($context);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_transactionFactory->create());
        $transactionCompleted = 0;

        try {
            foreach ($collection->getItems() as $transaction) {
                if ($transaction->getAction() == 'order/refund') {
                    continue;
                }
                $transaction->complete();
                $transactionCompleted++;
            }
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) have been checked.', $transactionCompleted)
            );
        } catch (Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }

        return $this->resultRedirectFactory->create()->setPath('affiliate/*/');
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageplaza_Affiliate::transaction');
    }
}
