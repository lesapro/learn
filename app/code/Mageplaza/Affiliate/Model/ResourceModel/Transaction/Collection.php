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

namespace Mageplaza\Affiliate\Model\ResourceModel\Transaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zend_Db_Select;

/**
 * Class Collection
 * @package Mageplaza\Affiliate\Model\ResourceModel\Transaction
 */
class Collection extends AbstractCollection
{
    /**
     * ID Field Name
     *
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
    protected $_eventObject = 'transaction_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\Affiliate\Model\Transaction', 'Mageplaza\Affiliate\Model\ResourceModel\Transaction');
    }

    /**
     * @param string $field
     *
     * @return string
     */
    public function getFieldTotal($field = 'amount')
    {
        $this->_renderFilters();

        $sumSelect = clone $this->getSelect();
        $sumSelect->reset(Zend_Db_Select::ORDER);
        $sumSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $sumSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $sumSelect->reset(Zend_Db_Select::COLUMNS);

        $sumSelect->columns("SUM(`$field`)");

        return $this->getConnection()->fetchOne($sumSelect, $this->_bindParams);
    }
}
