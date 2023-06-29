define([], function () {
    'use strict';

    return {
        /**
         * @return {Object}
         */
        getRules: function () {
            return {
                'postcode': {
                    'required': false
                },
                'country_id': {
                    'required': true
                },
                'region_id': {
                    'required': false
                },
                'region_id_input': {
                    'required': false
                }
            };
        }
    };
});
