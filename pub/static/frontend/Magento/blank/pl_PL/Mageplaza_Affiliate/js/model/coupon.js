/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
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
define([
    'ko',
    'domReady!'
], function (ko) {
    'use strict';

    var affiliateCouponCode = ko.observable(null),
        isApplied           = ko.observable(null);

    return {
        affiliateCouponCode: affiliateCouponCode,
        isApplied: isApplied,
        isLoading: ko.observable(false),

        /**
         * @return {*}
         */
        getAffiliateCouponCode: function () {
            return affiliateCouponCode;
        },

        /**
         * @return {Boolean}
         */
        getIsApplied: function () {
            return isApplied;
        }
    };
});
