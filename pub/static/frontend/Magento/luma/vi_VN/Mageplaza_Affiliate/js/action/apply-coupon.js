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
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Mageplaza_Affiliate/js/model/resource-url-manager',
    'Magento_Checkout/js/model/error-processor',
    'Mageplaza_Affiliate/js/model/messageList',
    'mage/storage',
    'mage/translate',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/recollect-shipping-rates',
    'Magento_Checkout/js/action/get-totals',
    'Mageplaza_Affiliate/js/model/coupon'
], function (ko, $, quote, urlManager, errorProcessor, messageContainer, storage, $t, getPaymentInformationAction,
             totals, fullScreenLoader, recollectShippingRates, getTotalsAction, couponModel
) {
    'use strict';

    return function (couponCode, isApplied) {
        var quoteId = quote.getQuoteId(),
            url     = urlManager.getApplyAffiliateCouponUrl(couponCode, quoteId),
            message = $t('Your affiliate coupon was successfully applied.'),
            data    = {},
            headers = {};

        fullScreenLoader.startLoader();

        return storage.put(
            url,
            data,
            false,
            null,
            headers
        ).done(function (response) {
            var deferred;

            if (response) {
                deferred = $.Deferred();

                isApplied(true);
                couponModel.isLoading(true);
                totals.isLoading(true);
                recollectShippingRates();
                if ($('body').hasClass('checkout-cart-index')) {
                    getTotalsAction([], deferred);
                } else {
                    getPaymentInformationAction(deferred);
                }
                $.when(deferred).done(function () {
                    fullScreenLoader.stopLoader();
                    totals.isLoading(false);
                    couponModel.isLoading(false);
                });
                messageContainer.addSuccessMessage({
                    'message': message
                });
            }
        }).fail(function (response) {
            fullScreenLoader.stopLoader();
            totals.isLoading(false);
            couponModel.isLoading(false);
            errorProcessor.process(response, messageContainer);
        });
    };

});
