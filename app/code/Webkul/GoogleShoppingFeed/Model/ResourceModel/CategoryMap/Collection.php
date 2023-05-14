<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */

    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\GoogleShoppingFeed\Model\CategoryMap::class,
            \Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap::class
        );
    }

    /**
     * _initSelect
     *
     * @return Collection
     */
    protected function _initSelect()
    {
        $this->addFilterToMap('store_category_name', 'secondTable.value');
        $this->addFilterToMap('entity_id', 'main_table.entity_id');
        parent::_initSelect();
    }
}
