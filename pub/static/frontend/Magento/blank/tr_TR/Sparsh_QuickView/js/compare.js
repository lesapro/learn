define(
    [
        'jquery'
    ], function ($) {
        'use strict';

        $.widget(
            'sparsh.compare', {
                _create: function () {
                    this.action();
                },

                action: function () {
                    var self = this;

                    $('#sparsh-mfp-quickview').delegate(
                        '.action.tocompare', 'click', function (e) {
                            var element = $(this),
                                dataPost = element.data('post'),
                                key = $('input[name="form_key"]').val();

                            if (key) {
                                dataPost.data.form_key = key;
                            }

                            var parameter = $.param(dataPost.data),
                                url = dataPost.action + (parameter.length ? '?' + parameter : '');

                            e.stopPropagation();

                            $.ajax(
                                {
                                    url: url,
                                    type: 'post',
                                    dataType: 'json',
                                    showLoader: true,
                                    success: function (res) {
                                        var popup = $('#sparsh-mfp-quickview');
                                        $('.sparsh-mfp-quickview-message').addClass('message-success success message').html('<div>' + res.message + '</div>');
                                        popup.animate({scrollTop: 0}, "slow");
                                        var checkExist = setInterval(function () {
                                            if ($('div.page.messages').find('.message-success').length) {
                                                clearInterval(checkExist);
                                                $('div.page.messages').find('.message-success').remove();
                                            }
                                        }, 50);
                                    }
                                }
                            );
                        }
                    );
                }
            }
        );

        return $.sparsh.compare;
    }
);

