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
    'Magento_Checkout/js/model/quote',
    'Mageplaza_Affiliate/js/model/resource-url-manager',
    'Magento_Checkout/js/model/error-processor',
    'Mageplaza_Affiliate/js/model/messageList',
    'mage/storage',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'mage/translate',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/recollect-shipping-rates',
    'Magento_Checkout/js/action/get-totals',
    'Mageplaza_Affiliate/js/model/coupon'
], function ($, quote, urlManager, errorProcessor, messageContainer, storage, getPaymentInformationAction, totals, $t,
             fullScreenLoader, recollectShippingRates, getTotalsAction, couponModel
) {
    'use strict';

    /**
     * Cancel applied coupon.
     *
     * @param {Boolean} isApplied
     * @returns {Deferred}
     */
    return function (isApplied) {
        var quoteId = quote.getQuoteId(),
            url     = urlManager.getCancelAffiliateCouponUrl(quoteId),
            message = $t('Your affiliate coupon was successfully removed.');

        messageContainer.clear();
        fullScreenLoader.startLoader();

        return storage.delete(
            url,
            false
        ).done(function () {
            var deferred = $.Deferred();

            totals.isLoading(true);
            couponModel.isLoading(true);
            recollectShippingRates();
            if ($('body').hasClass('checkout-cart-index')) {
                getTotalsAction([], deferred);
            } else {
                getPaymentInformationAction(deferred);
            }
            $.when(deferred).done(function () {
                isApplied(false);
                totals.isLoading(false);
                couponModel.isLoading(false);
                fullScreenLoader.stopLoader();
            });
            messageContainer.addSuccessMessage({
                'message': message
            });
        }).fail(function (response) {
            totals.isLoading(false);
            couponModel.isLoading(false);
            fullScreenLoader.stopLoader();
            errorProcessor.process(response, messageContainer);
        });
    };
});
