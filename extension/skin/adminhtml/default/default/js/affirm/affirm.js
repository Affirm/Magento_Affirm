/*global window, document, $, Ajax, setLocation */
var AFFIRM_AFFIRM = AFFIRM_AFFIRM || {};
(function () {
    'use strict';
    AFFIRM_AFFIRM.tool = {
        /**
         * Validate compatibility
         *
         * @param string url
         */
        validateCompatibility: function (url) {
            new Ajax.Request(url, {
                parameters: {},
                onSuccess: function (response) {
                    try {
                        var data = JSON.parse(response.responseText);
                        if (data.success) {
                            if (typeof data.validation_event_result !== 'undefined') {
                                $('validation_event').update(data.validation_event_result);
                            }
                            if (typeof data.validation_class_result !== 'undefined') {
                                $('validation_class').update(data.validation_class_result);
                            }
                            if (typeof data.validation_compiler_result !== 'undefined') {
                                $('validation_compiler').update(data.validation_compiler_result);
                            }
                            $('error_validation_message').update('');
                        } else{
                            $('error_validation_message').update(Translator.translate('Error occurred while validation ...'));
                        }
                    } catch (e) {
                        setLocation(window.location.href);
                    }
                }
            });
        }
    };
})();
