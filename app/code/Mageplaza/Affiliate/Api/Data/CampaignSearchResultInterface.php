<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Affiliate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Affiliate\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Withdraw search result interface.
 * @api
 */
interface CampaignSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Mageplaza\Affiliate\Api\Data\CampaignInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Mageplaza\Affiliate\Api\Data\CampaignInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items = null);
}
