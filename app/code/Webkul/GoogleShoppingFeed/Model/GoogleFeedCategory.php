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
use Webkul\GoogleShoppingFeed\Api\Data\GoogleFeedCategoryInterface;

class GoogleFeedCategory extends \Magento\Framework\Model\AbstractModel implements GoogleFeedCategoryInterface
{
    const CACHE_TAG = 'google_feed_category';

    /**
     * @var string
     */
    protected $_cacheTag = 'google_feed_category';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'google_feed_category';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\GoogleShoppingFeed\Model\ResourceModel\GoogleFeedCategory::class);
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
     * Get getLevel0.
     *
     * @return int
     */
    public function getLevel0()
    {
        return $this->getData(self::LEVEL0);
    }

    /**
     * Set LEVEL0.
     *
     * @param string $level0
     */
    public function setLevel0($level0)
    {
        return $this->setData(self::LEVEL0, $level0);
    }

    /**
     * Get getLevel1.
     *
     * @return string
     */
    public function getLevel1()
    {
        return $this->getData(self::LEVEL1);
    }

    /**
     * Set Level1.
     *
     * @param string $level1
     */
    public function setLevel1($level1)
    {
        return $this->setData(self::LEVEL1, $level1);
    }

    /**
     * Get getLevel2.
     *
     * @return string
     */
    public function getLevel2()
    {
        return $this->getData(self::LEVEL2);
    }

    /**
     * Set Level2.
     *
     * @param string $level2
     */
    public function setLevel2($level2)
    {
        return $this->setData(self::LEVEL2, $level2);
    }

    /**
     * Get getLevel3.
     *
     * @return string
     */
    public function getLevel3()
    {
        return $this->getData(self::LEVEL3);
    }

    /**
     * Set Level3.
     *
     * @param string $level3
     */
    public function setLevel3($level3)
    {
        return $this->setData(self::LEVEL3, $level3);
    }

    /**
     * Get getLevel4.
     *
     * @return string
     */
    public function getLevel4()
    {
        return $this->getData(self::LEVEL4);
    }

    /**
     * Set Level4.
     *
     * @param string $level4
     */
    public function setLevel4($level4)
    {
        return $this->setData(self::LEVEL4, $level4);
    }

    /**
     * Get getLevel5.
     *
     * @return string
     */
    public function getLevel5()
    {
        return $this->getData(self::LEVEL5);
    }

    /**
     * Set Level5.
     *
     * @param string $level5
     */
    public function setLevel5($level5)
    {
        return $this->setData(self::LEVEL5, $level5);
    }

    /**
     * Get getLevel6.
     *
     * @return string
     */
    public function getLevel6()
    {
        return $this->getData(self::LEVEL6);
    }

    /**
     * Set Level6.
     *
     * @param string $level6
     */
    public function setLevel6($level6)
    {
        return $this->setData(self::LEVEL6, $level6);
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
