define([
        'jquery',
        'mage/translate',
        'underscore',
        'Magento_Catalog/js/product/view/product-ids-resolver',
        'jquery-ui-modules/widget'
    ],
    function ($, $t, _, idsResolver) {
        'use strict';

        return function (catalogAddToCart) {
            $.widget('mage.catalogAddToCart', catalogAddToCart, {
                _redirect: function (url) {
                    var urlParts, locationParts, forceReload;

                    if($("body").hasClass('btn-buy-now-body')) {
                        window.top.location.href    =   url;
                    }

                    urlParts = url.split('#');
                    locationParts = window.location.href.split('#');
                    forceReload = urlParts[0] === locationParts[0];

                    window.location.assign(url);

                    if (forceReload) {
                        window.location.reload();
                    }
                }
            });

            return catalogAddToCart;
        };
    }
);
