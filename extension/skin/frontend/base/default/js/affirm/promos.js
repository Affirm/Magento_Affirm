/*
 *
 *  * BSD 3-Clause License
 *  *
 *  * Copyright (c) 2018, Affirm
 *  * All rights reserved.
 *  *
 *  * Redistribution and use in source and binary forms, with or without
 *  * modification, are permitted provided that the following conditions are met:
 *  *
 *  *  Redistributions of source code must retain the above copyright notice, this
 *  *   list of conditions and the following disclaimer.
 *  *
 *  *  Redistributions in binary form must reproduce the above copyright notice,
 *  *   this list of conditions and the following disclaimer in the documentation
 *  *   and/or other materials provided with the distribution.
 *  *
 *  *  Neither the name of the copyright holder nor the names of its
 *  *   contributors may be used to endorse or promote products derived from
 *  *   this software without specific prior written permission.
 *  *
 *  * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 *  * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 *  * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 *  * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 *  * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 *  * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 *  * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 *  * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 *  * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

/*global window, document, $, Ajax, setLocation */
var AFFIRM_AFFIRM = AFFIRM_AFFIRM || {};
(function (AFFIRM_AFFIRM) {
    'use strict';

    AFFIRM_AFFIRM.promos = AFFIRM_AFFIRM.promos || {};
    AFFIRM_AFFIRM.promos = {

        /**
         * Is initialized
         *
         * {Boolean}
         */
        isInitialized: false,

        /**
         * Is loaded
         *
         * {Boolean}
         */
        isLoaded: false,

        /**
         * Is As Low As initialized
         *
         * {bool}
         */
        isALAInitialized: false,

        /**
         * Options for configurable product
         *
         * {Object}
         */
        configurableOptions: {},

        /**
         * Is conf options initialized
         *
         * {bool}
         */
        isConfigurableOptionsInitialized: false,

        /**
         * isDBPromoConfigurableInitialized
         *
         * {bool}
         */
        isDBPromoConfigurableInitialized: false,

        /**
         * deBounceUpdatePromoConfigurable
         *
         * {Object}
         */
        deBounceUpdatePromoConfigurable: {},

        /**
         * Config
         *
         * {Object}
         */
        config: {},

        /**
         * Delay
         *
         * @type {Number}
         */
        REQUEST_CONFIGURABLE_DELAY: 1200,

        /**
         * Initialize
         *
         * @param {String} apiKey
         * @param {String} affirmJsUrl
         * @param {String} sessionId
         * @returns {AFFIRM_AFFIRM.promos}
         */
        initialize: function(apiKey, affirmJsUrl, sessionId) {
            sessionId = (typeof sessionId !== 'undefined') ? sessionId : 'null';
            this.config = {
                public_api_key: apiKey,
                script:         affirmJsUrl,
                session_id:     sessionId
            };
            this.setIsInitialized(true);

            return this;
        },

        /**
         * Load script

         * @returns {AFFIRM_AFFIRM.promos}
         */
        loadScript: function() {
            (function(l,g,m,e,a,f,b){var d,c=l[m]||{},
                h=document.createElement(f),
                n=document.getElementsByTagName(f)[0],
                k=function(a,b,c){
                    return function(){a[b]._.push([c,arguments])}
                };
                c[e]=k(c,e,"set");
                d=c[e];c[a]={};c[a]._=[];d._=[];c[a][b]=k(c,a,b);a=0;
                for(b="set add save post open empty reset on off trigger ready setProduct".split(" ");
                    a<b.length;a++)d[b[a]]=k(c,e,b[a]);a=0;for(b=["get","token","url","items"];
                                                               a<b.length;a++)d[b[a]]=function(){};h.async=!0;
                h.src=g[f];n.parentNode.insertBefore(h,n);
                delete g[f];d(g);l[m]=c})(window,this.config,
                    "affirm","checkout","ui","script","ready");
            this.setIsLoaded(true);
            return this;
        },

        /**
         * Set loaded flag
         *
         * @param {Boolean} flag
         * @returns {AFFIRM_AFFIRM.promos}
         */
        setIsLoaded: function(flag) {
            this.isLoaded = flag;
            return this;
        },

        /**
         * Get is script loaded
         *
         * @returns {Boolean}
         */
        getIsScriptLoaded: function() {
            return this.isLoaded;
        },

        /**
         * Set initialized flag
         *
         * @param {Boolean} flag
         * @returns {AFFIRM_AFFIRM.promos}
         */
        setIsInitialized: function(flag) {
            this.isInitialized = flag;
            return this;
        },

        /**
         * Get initialized flag
         *
         * @returns {Boolean}
         */
        getIsInitialized: function() {
            return this.isInitialized;
        },

        /**
         * Set ALA initialized flag
         *
         * @returns {Boolean}
         */
        getIsAsLowAsInitialized: function() {
            return this.isALAInitialized;
        },

        /**
         * Update promo blocks visibility
         */
        updatePromoBlocksVisibility: function(visibility) {
            var asLowAs = document.getElementById('learn-more');
            if (asLowAs) {
                asLowAs.style.visibility = visibility;
            }
            var promoBanners = document.getElementsByClassName('affirm-promo');
            if (promoBanners[0]){
                promoBanners[0].style.visibility = visibility;
            }
            return true;
        },

        /**
         * Init configurable options
         *
         * @returns {AFFIRM_AFFIRM.promos}
         */
        initConfigurableOptions: function(configurableInfo) {
            this.configurableOptions = configurableInfo;
            this.isConfigurableOptionsInitialized = true;
            return this;
        },

        /**
         * Get isConfigurableOptionsInitialized flag
         *
         * @returns {Boolean}
         */
        getIsConfigurableOptionsInitialized: function() {
            return this.isConfigurableOptionsInitialized;
        },

        /**
         * Update for configurable
         *
         * @returns {boolean}
         */
        hidePromoForConfigurableBackOrdered: function() {
            var selectedSimpleOptions = {}, productId, key, productProperties, productSelected;
            $$('select.super-attribute-select').each(function(item, index){
                selectedSimpleOptions[item.attributeId] = item.value;
            });
            for (productId in this.configurableOptions) {
                productSelected = true;
                productProperties = this.configurableOptions[productId];
                for (key in selectedSimpleOptions) {
                    if (productProperties[key] != selectedSimpleOptions[key]) {
                        productSelected = false;
                    }
                }
                if (productSelected && productProperties.backorders) {
                    this.updatePromoBlocksVisibility('hidden');
                    return true;
                }
            }
            return false;
        },

        /** Returns a function, that, as long as it continues to be invoked, will not
         * be triggered. The function will be called after it stops being called for
         * N milliseconds. If `immediate` is passed, trigger the function on the
         * leading edge, instead of the trailing.
         *
         * @param {Function} func
         * @param {Number} wait
         * @param {Boolean} immediate
         */
        deBounce: function (func, wait, immediate) {
            var timeout;
            return function () {
                var context = this,
                    args = arguments,
                    callNow,
                    later;
                later = function () {
                    timeout = null;
                    if (!immediate) {
                        func.apply(context, args);
                    }
                };
                callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) {
                    func.apply(context, args);
                }
            };
        },

        /**
         * Get de bounce singleton object
         *
         * @returns {Object}
         */
        getDeBouncedPromoUpdateConfigurable: function () {
            if (!this.isDBPromoConfigurableInitialized) {
                this.deBounceUpdatePromoConfigurable = this.deBounce(
                    this.hidePromoForConfigurableBackOrdered, this.REQUEST_CONFIGURABLE_DELAY, false
                ).bind(this);
                this.isDBPromoConfigurableInitialized = true;
            }
            return this.deBounceUpdatePromoConfigurable;
        }
    };
})(AFFIRM_AFFIRM);

/**
 * Product options wrapper
 *
 * Override to update as low as on PDP.
 *
 * @returns {string}
 */
if ((typeof Product != 'undefined') && (typeof Product.OptionsPrice != 'undefined') &&
    (typeof Product.OptionsPrice.prototype.reload != 'undefined')
) {
    Product.OptionsPrice.prototype.reload  = Product.OptionsPrice.prototype.reload.wrap(
        function(reload) {
            reload();
            // Affirm changes region start
            if (AFFIRM_AFFIRM.promos.getIsAsLowAsInitialized()) {
                var deBounceUpdateAsLowAs, container = $(this.containers[0]);
                deBounceUpdateAsLowAs = AFFIRM_AFFIRM.promos.getDeBouncedAsLowAs();
                deBounceUpdateAsLowAs(0, true, container, this.priceFormat);
            }
            if (AFFIRM_AFFIRM.promos.getIsConfigurableOptionsInitialized()) {
                var deBounceUpdatePromoConfigurable;
                deBounceUpdatePromoConfigurable = AFFIRM_AFFIRM.promos.getDeBouncedPromoUpdateConfigurable();
                if (!deBounceUpdatePromoConfigurable()) {
                    AFFIRM_AFFIRM.promos.updatePromoBlocksVisibility('visible');
                }
            }
            //endregion
        }
    );
}
