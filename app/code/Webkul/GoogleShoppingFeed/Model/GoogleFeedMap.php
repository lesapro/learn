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
use Webkul\GoogleShoppingFeed\Api\Data\GoogleFeedMapInterface;

class GoogleFeedMap extends \Magento\Framework\Model\AbstractModel implements GoogleFeedMapInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'google_feed_product_map';

    /**
     * @var string
     */
    protected $_cacheTag = 'google_feed_product_map';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'google_feed_product_map';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\GoogleShoppingFeed\Model\ResourceModel\GoogleFeedMap::class);
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
     * Get FeedId.
     *
     * @return int
     */
    public function getFeedId()
    {
        return $this->getData(self::FEED_ID);
    }

    /**
     * Set FeedId.
     *
     * @param int $feedId
     */
    public function setFeedId($feedId)
    {
        return $this->setData(self::FEED_ID, $feedId);
    }

    /**
     * Get MageProId.
     *
     * @return string
     */
    public function getMageProId()
    {
        return $this->getData(self::MAGE_PRO_ID);
    }

    /**
     * Set MageProId.
     *
     * @param int $mageProId
     */
    public function setMageProId($mageProId)
    {
        return $this->setData(self::MAGE_PRO_ID, $mageProId);
    }

    /**
     * Get ExpiredAt.
     *
     * @return string
     */
    public function getExpiredAt()
    {
        return $this->getData(self::EXPIRED_AT);
    }

    /**
     * Set ExpiredAt.
     *
     * @param string $expiredAt
     */
    public function setExpiredAt($expiredAt)
    {
        return $this->setData(self::EXPIRED_AT, $expiredAt);
    }
}
