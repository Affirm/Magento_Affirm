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
         * Config
         *
         * {Object}
         */
        config: {},

        /**
         * Initialize
         *
         * @param {String} apiKey
         * @param {String} affirmJsUrl
         * @returns {AFFIRM_AFFIRM.promos}
         */
        initialize: function(apiKey, affirmJsUrl) {
            this.config = {
                public_api_key: apiKey,
                script:         affirmJsUrl
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
        }
    };
})(AFFIRM_AFFIRM);
