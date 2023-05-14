<?php
/**
 * Webkul GoogleShoppingFeed CategoryMap Collection
 * @category  Webkul
 * @package   Webkul_GoogleShoppingFeed
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;
use Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap\Collection as CategoryMapCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Collection
 * Collection for displaying grid
 */
class Collection extends CategoryMapCollection implements SearchResultInterface
{
    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $entityAttribute
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param string $mainTable
     * @param string $eventPrefix
     * @param Object $eventObject
     * @param Object $resourceModel
     * @param Object $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
     * @param AbstractDb $resource = null
     * @return $this
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $entityAttribute,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->entityAttribute = $entityAttribute;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * Get Aggregations
     *
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * Set Aggregations
     *
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
    ) {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $joinCloumn = $this->getConnection()->tableColumnExists(
            $this->getTable('catalog_category_entity_varchar'),
            'entity_id'
        ) ? 'entity_id' : 'row_id';
        $nameAttrId = $this->entityAttribute->getIdByCode('catalog_category', 'name');
        $this->getSelect()->joinLeft(
            ['secondTable' => $this->getTable('catalog_category_entity_varchar')],
            'main_table.store_category_id = secondTable.'.$joinCloumn.' AND secondTable.store_id = 0
                    AND secondTable.attribute_id ='.$nameAttrId,
            [
                'store_category_name' => 'secondTable.value',
                'entity_id' => 'main_table.entity_id'
            ]
        );
        parent::_renderFiltersBefore();
    }
}
