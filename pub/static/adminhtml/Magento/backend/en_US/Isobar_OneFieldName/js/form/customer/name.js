define([
        'jquery',
        'uiRegistry',
        'mage/url',
        'Magento_Ui/js/form/element/abstract'
    ], function ($, uiRegistry, urlBuilder, abstract) {
        'use strict';

        return abstract.extend({

            /**
             * Initialize.
             */
            initialize: function () {
                this._super();

                this.handleNameField();

                return this;
            },

            /**
             * Handle name fields
             */
            handleNameField: function () {

                $(document).on('change', 'select[name="customer[website_id]"]', function () {
                    let url = window.location.origin + '/admin/onefieldname/customer/show';
                    let websiteIdField = uiRegistry.get('index = website_id');
                    let websiteId = websiteIdField.get('value');

                    $.ajax({
                        url: url,
                        showLoader: true,
                        method: 'POST',
                        data: {
                            websiteId: websiteId
                        },
                        success: function (data) {
                            $('div[data-index="firstname"]')
                                .children('.admin__field-label')
                                .children('label').children('span')
                                .text(data['label']);

                            let lastName = uiRegistry.get('index = lastname');
                            if (lastName !== undefined) {
                                if (data['is_show']) {
                                    lastName.hide();
                                } else {
                                    lastName.show();
                                }
                            }
                        }
                    });
                });
            }
        });
    }
);
