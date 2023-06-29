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
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Mageplaza_Affiliate/js/action/apply-coupon',
    'Mageplaza_Affiliate/js/action/cancel-coupon',
    'Mageplaza_Affiliate/js/model/coupon'
], function ($, ko, Component, quote, setCouponCodeAction, cancelCouponAction, affiliateCoupon) {
    'use strict';

    var totals              = quote.getTotals(),
        affiliateCouponCode = affiliateCoupon.getAffiliateCouponCode(),
        isApplied           = affiliateCoupon.getIsApplied(),
        affiliateSource     = $.mage.cookies.get('affiliate_source'),
        affiliateKey        = $.mage.cookies.get('affiliate_key');

    if (totals() && affiliateSource === 'coupon' && affiliateKey) {
        affiliateCouponCode(affiliateKey);
    }

    isApplied(affiliateCouponCode() != null);

    return Component.extend({
        defaults: {
            template: 'Mageplaza_Affiliate/checkout/payment-coupon'
        },
        affiliateCouponCode: affiliateCouponCode,
        isLoading: affiliateCoupon.isLoading,

        /**
         * Applied flag
         */
        isApplied: isApplied,

        /**
         * Coupon code application procedure
         */
        apply: function () {
            if (this.validate()) {
                setCouponCodeAction(affiliateCouponCode(), isApplied);
            }
        },

        /**
         * Cancel using coupon
         */
        cancel: function () {
            if (this.validate()) {
                affiliateCouponCode('');
                cancelCouponAction(isApplied);
            }
        },

        /**
         * Coupon form validation
         *
         * @returns {Boolean}
         */
        validate: function () {
            var form = '#affiliate-coupon-discount-form';

            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
