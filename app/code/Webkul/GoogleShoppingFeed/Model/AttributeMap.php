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
use Webkul\GoogleShoppingFeed\Api\Data\AttributeMapInterface;

class AttributeMap extends \Magento\Framework\Model\AbstractModel implements AttributeMapInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'google_shopping_field_feeds';

    /**
     * @var string
     */
    protected $_cacheTag = 'google_shopping_field_feeds';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'google_shopping_field_feeds';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\GoogleShoppingFeed\Model\ResourceModel\AttributeMap::class);
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
     * Get AttributeCode.
     *
     * @return int
     */
    public function getAttributeCode()
    {
        return $this->getData(self::ATTRIBUTE_CODE);
    }

    /**
     * Set AttributeCode.
     *
     * @param string $attributeCode
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->setData(self::ATTRIBUTE_CODE, $attributeCode);
    }

    /**
     * Get GoogleFeedField.
     *
     * @return int
     */
    public function getGoogleFeedField()
    {
        return $this->getData(self::GOOGLE_FEED_FIELD);
    }

    /**
     * Set AttributeCode.
     *
     * @param string $googleFeedField
     */
    public function setGoogleFeedField($googleFeedField)
    {
        return $this->setData(self::GOOGLE_FEED_FIELD, $googleFeedField);
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
