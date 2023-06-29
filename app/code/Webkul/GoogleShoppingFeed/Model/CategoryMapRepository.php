<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Model;

use Webkul\GoogleShoppingFeed\Api\Data\CategoryMapInterface;
use Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap\Collection;

/**
 * GoogleShoppingFeed CategoryMapRepository
 */
class CategoryMapRepository implements \Webkul\GoogleShoppingFeed\Api\CategoryMapRepositoryInterface
{
    /**
     * @var \Webkul\GoogleShoppingFeed\Model\ResourceModel\Accounts
     */
    protected $resourceModel;

    /**
     * @param AttributeMapFactory $attributeMapFactory
     * @param \Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap\CollectionFactory $collectionFactory
     * @param \Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap $resourceModel
     */
    public function __construct(
        AttributeMapFactory $attributeMapFactory,
        \Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap\CollectionFactory $collectionFactory,
        \Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap $resourceModel
    ) {
        $this->resourceModel = $resourceModel;
        $this->attributeMapFactory = $attributeMapFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get collection by account id
     *
     * @param  int $accountId
     * @return AttributeMapFactory
     */
    public function getCollectionById($accountId)
    {
        $attributeMap = $this->attributeMapFactory->create()->load($accountId);
        return $attributeMap;
    }
}
