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
    'jquery'
], function ($) {
    'use strict';

    return function (target) {

        $.validator.addMethod(
            'mp-validate-symbol',
            function (value, elm) {

                if ($.mage.isEmptyNoTrim(value)) {
                    return true;
                }

                var className, result, range, m, classes, ii;

                className = /^mp-validate-symbol-(-?.+)?$/;
                result = true;

                if (elm && elm.className) {
                    classes = elm.className.split(' ');
                    ii = classes.length;

                    while (ii--) {
                        range = classes[ii];
                        m = className.exec(range);

                        if (m) {
                            if (value.indexOf(m[1]) >= 0) {
                                result = false;
                            }
                        }
                    }
                }

                return result;
            },

            $.mage.__('Please enter a param not including the dot or hash symbol.')
        );

        return target;
    };
});
