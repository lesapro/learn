<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Model;

use Webkul\GoogleShoppingFeed\Api\Data\AttributeMapInterface;
use Webkul\GoogleShoppingFeed\Model\ResourceModel\Accounts\Collection;

/**
 * GoogleShoppingFeed AttributeMapRepository
 */
class AttributeMapRepository implements \Webkul\GoogleShoppingFeed\Api\AttributeMapRepositoryInterface
{
    /**
     * @var \Webkul\GoogleShoppingFeed\Model\ResourceModel\Accounts
     */
    protected $resourceModel;

    /**
     * @param AttributeMapFactory $attributeMapFactory
     * @param \Webkul\GoogleShoppingFeed\Model\ResourceModel\AttributeMap\CollectionFactory $collectionFactory
     * @param \Webkul\GoogleShoppingFeed\Model\ResourceModel\AttributeMap $resourceModel
     */
    public function __construct(
        AttributeMapFactory $attributeMapFactory,
        \Webkul\GoogleShoppingFeed\Model\ResourceModel\AttributeMap\CollectionFactory $collectionFactory,
        \Webkul\GoogleShoppingFeed\Model\ResourceModel\AttributeMap $resourceModel
    ) {
        $this->resourceModel = $resourceModel;
        $this->attributeMapFactory = $attributeMapFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get collection by account id
     *
     * @param  int $accountId
     * @return object
     */
    public function getCollectionById($accountId)
    {
        $accountDetails = $this->attributeMapFactory->create()->load($accountId);
        return $accountDetails;
    }
}
