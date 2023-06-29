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

namespace Mageplaza\Affiliate\Block\Account\Home;

use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Affiliate\Block\Account\Home;

/**
 * Class Transaction
 * @package Mageplaza\Affiliate\Block\Account\Home
 */
class Transaction extends Home
{
    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getTransactions()
    {
        $collection = $this->transactionFactory->create()
            ->getCollection()
            ->addFieldToFilter('account_id', $this->getCurrentAccount()->getId());
        $collection->setOrder('transaction_id', 'DESC');

        if ($collection->getSize()) {
            // create pager block for collection
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'affiliate.transaction.pager');
            // assign collection to pager
            $limit = $this->_request->getParam('limit') ?: 10;
            $pager->setLimit($limit)->setCollection($collection);
            $this->setChild('pager', $pager);// set pager block in layout
        }

        return $collection;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * get customer email by order id
     *
     * @param $orderId
     *
     * @return string
     */
    public function getCustomerEmailByOrderId($orderId)
    {
        return $this->_affiliateHelper->getCustomerEmailByOId($orderId);
    }
}
