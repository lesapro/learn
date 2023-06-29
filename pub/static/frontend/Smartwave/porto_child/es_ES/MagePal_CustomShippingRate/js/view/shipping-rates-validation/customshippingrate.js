define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    '../../model/shipping-rates-validator/customshippingrate',
    '../../model/shipping-rates-validation-rules/customshippingrate'
], function (Component,
             defaultShippingRatesValidator,
             defaultShippingRatesValidationRules,
             customShippingRatesValidator,
             customShippingRatesValidationRules) {
    'use strict';

    defaultShippingRatesValidator.registerValidator('customshippingrate', customShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('customshippingrate', customShippingRatesValidationRules);

    return Component;
});
