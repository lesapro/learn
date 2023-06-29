<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Api\Data;

interface GoogleFeedMapInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ID = 'entity_id';
    const FEED_ID = 'feed_id';
    const MAGE_PRO_ID = 'mage_pro_id';
    const EXPIRED_AT = 'expired_at';

    /**
     * Get ID.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID.
     *
     * @param int $entityId
     * @return $this
     */
    public function setId($entityId);

    /**
     * Get FeedId.
     *
     * @return string
     */
    public function getFeedId();

    /**
     * Set FeedId.
     *
     * @param int $feedId
     * @return $this
     */
    public function setFeedId($feedId);

    /**
     * Get MageProId.
     *
     * @return string
     */
    public function getMageProId();

    /**
     * Set MageProId.
     *
     * @param int $mageProId
     * @return $this
     */
    public function setMageProId($mageProId);

    /**
     * Get ExpiredAt.
     *
     * @return string
     */
    public function getExpiredAt();

    /**
     * Set ExpiredAt.
     *
     * @param string $expiredAt
     * @return $this
     */
    public function setExpiredAt($expiredAt);
}
