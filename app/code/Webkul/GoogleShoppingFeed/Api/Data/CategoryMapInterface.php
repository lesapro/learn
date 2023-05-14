<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Api\Data;

interface CategoryMapInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ID = 'entity_id';
    const STORE_CATEGORY_ID = 'store_category_id';
    const GOOGLE_FEED_CATEGORY = 'google_feed_category';
    const CREATED_AT = 'created_at';

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
     * Get StoreCategoryId.
     *
     * @return string
     */
    public function getStoreCategoryId();

    /**
     * Set StoreCategoryId.
     *
     * @param int $storeCategoryId
     * @return $this
     */
    public function setStoreCategoryId($storeCategoryId);

    /**
     * Get GoogleFeedCategory.
     *
     * @return string
     */
    public function getGoogleFeedCategory();

    /**
     * Set GoogleFeedCategory.
     *
     * @param string $googleFeedCategory
     * @return $this
     */
    public function setGoogleFeedCategory($googleFeedCategory);

    /**
     * Get CreatedAt.
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set CreatedAt.
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
}
