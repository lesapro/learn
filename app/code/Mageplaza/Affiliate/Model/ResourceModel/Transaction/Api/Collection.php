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

namespace Mageplaza\Affiliate\Model\ResourceModel\Transaction\Api;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Mageplaza\Affiliate\Api\Data\TransactionSearchResultInterface;

/**
 * Class Collection
 * @api
 * @package Mageplaza\Affiliate\Model\ResourceModel\Transaction\Api
 */
class Collection extends AbstractCollection implements TransactionSearchResultInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'transaction_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'affiliate_transaction_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'affiliate_transaction_collection';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Mageplaza\Affiliate\Model\Api\Transaction',
            'Mageplaza\Affiliate\Model\ResourceModel\Transaction'
        );
    }
}
