/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license sliderConfig is
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

define(
    [
        'jquery',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, resourceUrlManager, quote) {
        "use strict";

        return $.extend(resourceUrlManager, {
            getApplyAffiliateCouponUrl: function (code) {
                var params = this.getCheckoutMethod() === 'guest' ? {quoteId: quote.getQuoteId()} : {},
                    urls   = {
                        'guest': '/guest-carts/:quoteId/mpaffiliatecoupons/' + code,
                        'customer': '/carts/mine/mpaffiliatecoupons/' + code
                    };

                return this.getUrl(urls, params);
            },

            /**
             * @param {String} quoteId
             * @return {*}
             */
            getCancelAffiliateCouponUrl: function (quoteId) {
                var params = this.getCheckoutMethod() === 'guest' ? {quoteId: quote.getQuoteId()} : {},
                    urls = {
                        'guest': '/guest-carts/' + quoteId + '/mpaffiliatecoupons/',
                        'customer': '/carts/mine/mpaffiliatecoupons/'
                    };

                return this.getUrl(urls, params);
            },
        });
    }
);
