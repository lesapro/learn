<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Webkul\GoogleShoppingFeed\Api\Data\CategoryMapInterface;

class CategoryMap extends \Magento\Framework\Model\AbstractModel implements CategoryMapInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'google_shopping_category_map';

    /**
     * @var string
     */
    protected $_cacheTag = 'google_shopping_category_map';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'google_shopping_category_map';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap::class);
    }
    /**
     * Get Id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set Id.
     *
     * @param int $entityId
     */
    public function setId($entityId)
    {
        return $this->setData(self::ID, $entityId);
    }

    /**
     * Get StoreCategoryId.
     *
     * @return int
     */
    public function getStoreCategoryId()
    {
        return $this->getData(self::STORE_CATEGORY_ID);
    }

    /**
     * Set StoreCategoryId.
     *
     * @param int $storeCategoryId
     */
    public function setStoreCategoryId($storeCategoryId)
    {
        return $this->setData(self::STORE_CATEGORY_ID, $storeCategoryId);
    }

    /**
     * Get GoogleFeedCategory.
     *
     * @return int
     */
    public function getGoogleFeedCategory()
    {
        return $this->getData(self::GOOGLE_FEED_CATEGORY);
    }

    /**
     * Set AttributeCode.
     *
     * @param string $googleFeedCategory
     */
    public function setGoogleFeedCategory($googleFeedCategory)
    {
        return $this->setData(self::GOOGLE_FEED_CATEGORY, $googleFeedCategory);
    }

    /**
     * Get CreatedAt.
     *
     * @return varchar
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set CreatedAt.
     *
     * @param string $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
