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

namespace Mageplaza\Affiliate\Block\Adminhtml\Transaction;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Mageplaza\Affiliate\Model\Transaction\Status;

/**
 * Class View
 * @package Mageplaza\Affiliate\Block\Adminhtml\Transaction
 */
class View extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * View constructor.
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Initialize Transaction edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mageplaza_Affiliate';
        $this->_controller = 'adminhtml_transaction';
        $this->_mode = 'view';

        parent::_construct();

        $transaction = $this->getTransaction();
        if ($transaction->getId()) {
            $this->buttonList->remove('save');
            $this->buttonList->remove('delete');
            if ($transaction->getStatus() != Status::STATUS_CANCELED) {
                $confirm = __('Are you sure you want to cancel this transaction?');
                $this->buttonList->update('reset', 'label', __('Cancel'));
                $this->buttonList->update('reset', 'class', 'cancel');
                $this->buttonList->update(
                    'reset',
                    'onclick',
                    'deleteConfirm(\'' . $confirm . '\', \'' . $this->getCancelUrl() . '\')'
                );
            }

            if ($transaction->getStatus() == Status::STATUS_HOLD) {
                $confirm = __('Are you sure you want to complete this transaction?');
                $this->addButton(
                    'complete',
                    [
                        'label' => __('Complete'),
                        'onclick' => 'deleteConfirm(\'' . $confirm . '\', \'' . $this->getCompleteUrl() . '\')',
                        'class' => 'complete'
                    ],
                    -1
                );
            }
        }
    }

    /**
     * Retrieve text for header element depending on loaded Transaction
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __("View Transaction '%1'", $this->escapeHtml($this->getTransaction()->getId()));
    }

    /**
     * Get Cancel Transaction url
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrl('affiliate/transaction/cancel', ['id' => $this->getTransaction()->getId()]);
    }

    /**
     * Get Complete Transaction url
     *
     * @return string
     */
    public function getCompleteUrl()
    {
        return $this->getUrl('affiliate/transaction/complete', ['id' => $this->getTransaction()->getId()]);
    }

    /**
     * @return mixed
     */
    public function getTransaction()
    {
        return $this->_coreRegistry->registry('current_transaction');
    }
}
