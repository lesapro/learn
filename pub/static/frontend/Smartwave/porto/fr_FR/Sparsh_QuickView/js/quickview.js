define(
    [
        'jquery',
        'mage/translate',
        'mage/url',
        'magnificPopup'
    ], function ($, $t, url) {
        "use strict";

        $.widget(
            'sparsh.QuickView', {
                options: {
                    baseUrl: '/',
                    popupTitle: $t('Quick View'),
                    itemClass: '.products.list .item.product-item, .widget-product-grid .product-item',
                    btnLabel: $t('Quick View'),
                    btnContainer: '.product-item-info',
                    handlerClassName: 'sparsh-quick-view-button',
                    defaultClassName: 'action primary'
                },
                _create: function () {
                    if (!$('body').hasClass('catalog-product-view')) {
                        this._initialButtons(this.options);
                        this._bindPopup(this.options);
                    }
                },
                _initialButtons: function (config) {
                    $(config.itemClass).each(function () {
                        if (!$(this).find('.' + config.handlerClassName).length) {
                            var groupName = $(this).parent().attr('class').replace(' ', '-');
                            var productId = $(this).find('.price-final_price').data('product-id');
                            if (typeof productId !== 'undefined' && productId !== undefined && productId !== null) {
                                var url = config.baseUrl + 'quickview/catalog_product/view/id/' + productId;
                                var btnQuickView = '<div class="sparsh-quick-view-btn-container">';
                                btnQuickView += '<a rel="' + groupName + '" class="' + config.defaultClassName + ' ' + config.handlerClassName + '" href="' + url + '"';
                                btnQuickView += ' title="' + config.popupTitle + '"';
                                btnQuickView += ' >';
                                btnQuickView += '<span>' + config.btnLabel + '</span></a>';
                                btnQuickView += '</div>';
                                $(this).find(config.btnContainer).prepend(btnQuickView);
                                $(this).addClass('sparsh-quick-view-item');
                            }
                        }
                    });
                },
                _bindPopup: function (config) {
                    var self = this;
                    $('.' + config.handlerClassName).each(
                        function () {
                            $(this).magnificPopup(
                                {
                                    type: 'ajax',
                                    closeOnContentClick: false,
                                    closeMarkup: '<button title="Close (Esc)" type="button" class="sparsh-mfp-close sparsh-mfp-quick-close"></button>',
                                    callbacks: {
                                        ajaxContentAdded: function () {
                                            $('.sparsh-mfp-content').trigger('contentUpdated');
                                            $('.sparsh-mfp-content').prop('id', 'sparsh-mfp-quickview');
                                            $('#sparsh-mfp-quickview').prepend('<div class="sparsh-mfp-quickview-message"></div>');
                                            if($('#sparsh-mfp-quickview').find('.table-wrapper.grouped').length){
                                                $('.sparsh-mfp-content').addClass('page-product-grouped');
                                            }
                                            if($('#sparsh-mfp-quickview').find('.field.downloads').length){
                                                $('.sparsh-mfp-content').addClass('page-product-downloadable');
                                            }
                                            $('.sparsh-mfp-content').addClass('catalog-product-view');
                                            if(!$('body').hasClass('page-layout-1column')){
                                                $('.sparsh-mfp-content').addClass('page-layout-1column');
                                                if($('.sparsh-mfp-content').hasClass('page-product-downloadable')){
                                                    $('.sparsh-mfp-content').addClass('sparsh-quickview-custom-options');
                                                }
                                            }
                                        }
                                    }
                                }
                            );
                        }
                    );

                    $(document).on(
                        'ajaxComplete', function (event, xhr, settings) {
                            if (settings.type.match(/get/i) && _.isObject(xhr.responseJSON)) {
                                var result = xhr.responseJSON;
                                if (_.isObject(result.cart) && _.isObject(result.messages)) {
                                    if(result.messages){
                                        if($('#sparsh-mfp-quickview').length) {
                                            var message = result.messages.messages[0];
                                            if(message){
                                                var popup = $('#sparsh-mfp-quickview');
                                                $('.sparsh-mfp-quickview-message').addClass('message-'+message.type+' '+message.type+' message').html('<div>' + message.text + '</div>');
                                                popup.animate({scrollTop: 0}, "slow");
                                                if($('div.page.messages').find('.message-'+message.type).length){
                                                    $('div.page.messages').find('.message-'+message.type).remove();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    );
                }
            }
        );
        return $.sparsh.QuickView;
    }
);

