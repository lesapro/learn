<?php

namespace MagePal\CustomShippingRate\Api\Data;

interface TotalsItemInterface extends \Magento\Quote\Api\Data\TotalsItemInterface
{
    /**
     * Item shop data.
     */
    const SHOP = 'shop';

    /**
     * Set totals item shop
     *
     * @param string $shop
     * @return TotalsItemInterface
     */
    public function setShop($shop);

    /**
     * Get totals item shop
     *
     * @return string Item shop
     */
    public function getShop();
}
