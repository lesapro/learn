/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/model/totals'
], function (ko, checkoutDataResolver, totals) {
    'use strict';

    var shippingRates = ko.observableArray([]);
    var shops = ko.observableArray([]);

    return {
        isLoading: ko.observable(false),
        /**
         * @param {Object} item
         * @return {null}
         */
        getSrc: function (item) {
            if (window.checkoutConfig.imageData[item['item_id']]) {
                return window.checkoutConfig.imageData[item['item_id']].src;
            }

            return null;
        },

        /**
         * @param {Object} item
         * @return {null}
         */
        getWidth: function (item) {
            if (window.checkoutConfig.imageData[item['item_id']]) {
                return window.checkoutConfig.imageData[item['item_id']].width;
            }

            return null;
        },

        /**
         * @param {Object} item
         * @return {null}
         */
        getHeight: function (item) {
            if (window.checkoutConfig.imageData[item['item_id']]) {
                return window.checkoutConfig.imageData[item['item_id']].height;
            }

            return null;
        },

        /**
         * @param {Object} item
         * @return {null}
         */
        getAlt: function (item) {
            if (window.checkoutConfig.imageData[item['item_id']]) {
                return window.checkoutConfig.imageData[item['item_id']].alt;
            }

            return null;
        },
        /**
         * Set shipping rates
         *
         * @param {*} ratesData
         */
        setShippingRates: function (ratesData) {
            let shoplist = {};
            for (let i = 0; i < ratesData.length; i++) {
                let method = ratesData[i]['method_code'];
                const shopStr = method.split('_')[1];
                if (!shoplist.hasOwnProperty(shopStr)) {
                    shoplist[shopStr] = [];
                    shoplist[shopStr]['rates'] = [];
                    shoplist[shopStr]['products'] = [];
                }
                ratesData[i].shop = shopStr;
                shoplist[shopStr]['rates'].push(ratesData[i]);
            }
            let totalItems = totals.getItems();
            var that = this;
            let includeProduct = function (item) {
                if (item['shop']) {
                    item.src = that.getSrc(item);
                    item.width = that.getWidth(item);
                    item.height = that.getHeight(item);
                    shoplist[item['shop']]['products'].push(item);
                }
            }
            totalItems().forEach(includeProduct);

            shops(shoplist);
            shops.valueHasMutated();

            shippingRates(ratesData);
            shippingRates.valueHasMutated();
            checkoutDataResolver.resolveShippingRates(ratesData);
        },

        /**
         * Get shipping rates
         *
         * @returns {*}
         */
        getShippingRates: function () {
            return shippingRates;
        },
        /**
         * Get shipping shops
         *
         * @returns {*}
         */
        getShops: function () {
            return shops;
        }
    };
});
