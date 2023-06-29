define(['Magento_Checkout/js/checkout-data'], function (checkoutData) {
    'use strict';

    return function (payload) {
        payload.addressInformation['extension_attributes'] = {};
        let shippingRates = checkoutData.getSelectedShippingRateExt();
        let rates = {
            'items' : shippingRates,
            'carrier_code': payload.addressInformation.shipping_carrier_code,
            'method_code':  payload.addressInformation.shipping_method_code
        };
        payload.addressInformation['extension_attributes'] = {'rates':JSON.stringify(rates)};
        return payload;
    };
});
