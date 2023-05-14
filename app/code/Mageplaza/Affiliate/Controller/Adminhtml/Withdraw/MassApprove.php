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

namespace Mageplaza\Affiliate\Controller\Adminhtml\Withdraw;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\Affiliate\Model\ResourceModel\Withdraw\CollectionFactory;
use Mageplaza\Affiliate\Model\ResourceModel\Withdraw\WithdrawFactory;
use Mageplaza\Affiliate\Model\Withdraw;
use Mageplaza\Affiliate\Model\Withdraw\Status;

/**
 * Class MassApprove
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Withdraw
 */
class MassApprove extends Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory|WithdrawFactory
     */
    protected $_collectionFactory;

    /**
     * MassApprove constructor.
     *
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Context $context
     */
    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory,
        Context $context
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $approve = 0;
        foreach ($collection as $withdraw) {
            /** @var Withdraw $withdraw */
            try {
                if ($withdraw->getStatus() != Status::CANCEL) {
                    $withdraw->setData('status', Status::COMPLETE);
                    $withdraw->save();
                    $approve++;
                }
            } catch (Exception $e) {
                $this->messageManager->addError(
                    __($e->getMessage())
                );
            }
        }
        $this->messageManager->addSuccessMessage(__(
            'A total of %1 record(s) have been approved successfully.',
            $approve
        ));
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
