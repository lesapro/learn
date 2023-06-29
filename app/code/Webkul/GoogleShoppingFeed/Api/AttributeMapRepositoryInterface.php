<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Api;

/**
 * @api
 */
interface AttributeMapRepositoryInterface
{
    /**
     * Get collection by account id
     *
     * @param  int $accountId
     * @return object
     */
    public function getCollectionById($accountId);
}
