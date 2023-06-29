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

namespace Mageplaza\Affiliate\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Affiliate\Model\TransactionFactory;

/**
 * Class Transaction
 * @package Mageplaza\Affiliate\Controller\Adminhtml
 */
abstract class Transaction extends AbstractAction
{
    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * Transaction constructor.
     *
     * @param Context $context
     * @param TransactionFactory $transactionFactory
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        TransactionFactory $transactionFactory,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->_transactionFactory = $transactionFactory;

        parent::__construct($context, $resultPageFactory, $coreRegistry);
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
