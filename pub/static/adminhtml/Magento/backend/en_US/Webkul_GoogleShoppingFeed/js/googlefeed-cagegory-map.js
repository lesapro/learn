/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

define([
    "jquery",
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    "jquery/ui",
    'mage/calendar',
], function ($, alert, $t) {
    "use strict";
    var popup,modelWrapper;
    $.widget('googleshoppingfeed.catmap', {
        _create: function () {
        }
    });
    return $.googleshoppingfeed.catmap;
});
