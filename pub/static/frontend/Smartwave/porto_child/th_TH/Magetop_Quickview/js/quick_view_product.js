/**
 * Magetop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magetop.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magetop.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magetop
 * @package     Magetop_Quickview
 * @copyright   Copyright (c) Magetop (https://www.magetop.com/)
 * @license     https://www.magetop.com/LICENSE.txt
 */
define(
    [
        'jquery',
        'mage/mage',
        'Magetop_Quickview/js/jquery.magnific-popup.min'
    ],
    function ($) {
        "use strict";
        $.widget(
            'magetop.quick_view_product',
            {
                options: {
                    productUrl: '',
                    buttonText: '',
                    isEnabled: false,
                    baseUrl: '',
                    productImageWrapper: '',
                    productItemInfo: ''
                },

                _create: function () {
                    this._EventListener();
                },
                _EventListener: function () {
                    var $widget = this;
                    if ($widget.options.isEnabled == 1) {
                        $('#fixed-box-buy-later').on('click', function () {
                            var id_product  =   $("input[name='product']").val();
                                var prodUrl = $widget.options.productUrl + 'id/' + id_product + '?type=buylater';
                                if (prodUrl.length) {
                                    $widget.openPopup(prodUrl);
                                }
                        });
                        $(document).on(
                            'click',
                            '#fixed-box-buy-now',
                            function () {
                                var id_product  =   $("input[name='product']").val();
                                var prodUrl = $widget.options.productUrl + 'id/' + id_product + '?type=buynow';
                                if (prodUrl.length) {
                                    $widget.openPopup(prodUrl);
                                }
                            }
                        );

                        $(document).on(
                            'click',
                            '#fixed-box-buy-later',
                            function () {
                                var id_product  =   $("input[name='product']").val();
                                var prodUrl = $widget.options.productUrl + 'id/' + id_product + '?type=buylater';
                                if (prodUrl.length) {
                                    $widget.openPopup(prodUrl);
                                }
                            }
                        );
                    }
                },

                openPopup: function (prodUrl) {
                    var $widget = this,
                        url = $widget.options.baseUrl + 'magetop_quickview/index/updatecart';

                    if (!prodUrl.length) {
                        return false;
                    }

                    $.magnificPopup.open(
                        {
                            items: {
                                src: prodUrl
                            },
                            type: 'iframe',
                            closeOnBgClick: true,
                            showCloseBtn: true,
                            scrolling: false,
                            preloader: true,
                            tLoading: '',
                            callbacks: {
                                open: function () {
                                    $('.mfp-preloader').css('display', 'block');
                                    $("iframe.mfp-iframe").contents().find("html").addClass("magetop_loader");
                                },
                                beforeClose: function () {
                                    $('[data-block="minicart"]').trigger('contentLoading');
                                    $.ajax(
                                        {
                                            url: url,
                                            method: "POST"
                                        }
                                    );
                                },
                                close: function () {
                                    $('.mfp-preloader').css('display', 'none');
                                }
                            }
                        }
                    );
                }
            }
        );
        return $.magetop.quick_view_product;
    }
);
