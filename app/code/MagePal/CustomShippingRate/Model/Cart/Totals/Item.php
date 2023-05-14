<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagePal\CustomShippingRate\Model\Cart\Totals;
use MagePal\CustomShippingRate\Api\Data\TotalsItemInterface;

/**
 * Cart item totals.
 *
 * @codeCoverageIgnore
 */
class Item extends \Magento\Quote\Model\Cart\Totals\Item implements TotalsItemInterface
{

    /**
     * Set totals item shop
     *
     * @param string $shop
     * @return Item
     */
    public function setShop($shop)
    {
        return $this->setData(self::SHOP, $shop);
    }

    /**
     * Get totals item shop
     *
     * @return string Item shop
     */
    public function getShop()
    {
        return $this->_get(self::SHOP);
    }
}
