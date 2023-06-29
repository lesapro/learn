define([
    'uiComponent',
    'jquery',
    'mage/url',
    'mage/cookies'
], function (Component, $, urlBuilder) {
    'use strict';

    return Component.extend({
        defaults: {
            noticeType: 1,
            template: 'Amasty_GdprCookie/cookiebar',
            settingsLink: '/',
            allowLink: '/',
            websiteInteraction: '0',
            firstShowProcess: '0'
        },
        initObservable: function () {
            this._super()
                .observe({
                    showDisallowButton: this.noticeType === 2
                });

            return this;
        },
        setupCookies: function () {
            location.href = urlBuilder.build(this.settingsLink);
        },
        allowCookies: function () {
            let self = this;
            $.ajax({
                showLoader: true,
                url: this.allowLink,
            }) .done(function () {
                $("#gdpr-cookie-block").remove();

                if (self.websiteInteraction == 1) {
                    self.restoreInteraction();
                }
            });
        },
        isShowNotificationBar: function () {
            if (this.noticeType === 0
                || $.mage.cookies.get('amcookie_allowed') !== null
                || !this.isNeedFirstShow()
            ) {
                return false;
            }
            this.blockInteraction();
            return true;
        },
        blockInteraction: function () {
            var cookie = $.mage.cookies.get('amcookie_allowed');
            if (cookie === null
                && this.websiteInteraction == 1
                && !$('.cms-cookie-policy').length
                && window.location.href + '/' !== this.settingsLink
            ) {
                $('.page-wrapper').css({
                    "pointer-events": "none",
                    "-webkit-user-select": "none",
                    "-moz-user-select": "none",
                    "-ms-user-select": "none",
                    "user-select": "none",
                    "height": "100%",
                    "overflow": "hidden",
                    "opacity": "0.1"
                });
            }
        },
        restoreInteraction: function () {
            if ($('.cms-cookie-policy').length === 0) {
                $('.page-wrapper').removeAttr('style');
            }
        },
        isNeedFirstShow: function () {
            if (this.firstShowProcess === "0") {
                return true
            }

            if (!localStorage.amCookieBarFirstShow) {
                localStorage.amCookieBarFirstShow = 1;
                return true;
            }

            return false;
        },
        closeCookie: function() {
            var self = this;
            $('#amgdprcookie-btn-save').click();
            $("#gdpr-cookie-block").remove();
        }
    });
});
