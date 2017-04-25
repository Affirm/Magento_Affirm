/*global window, document, $, Ajax, setLocation */
var AFFIRM_AFFIRM = AFFIRM_AFFIRM || {};
(function (AFFIRM_AFFIRM) {
    'use strict';

    AFFIRM_AFFIRM.promos = AFFIRM_AFFIRM.promos || {};
     var asLowAs = {

        /**
         * Options for AsLowAs
         *
         * {Object}
         */
        options: {},

        /**
         * De bounced object
         *
         * {Object}
         */
        deBounceUpdateAsLowAs: {},

        /**
         * Is de bounce initialized
         *
         * {bool}
         */
        isDBInitialized: false,

        /**
         * Delay before request to affirm
         *
         * @type {Number}
         */
        REQUEST_DELAY: 1000,

         /**
          * Min total
          *
          * @type {Number}
          */
         minTotal: null,

         /**
          * Max total
          *
          * @type {Number}
          */
         maxTotal: null,

        /**
         * Init options
         *
         * @param {String} apr
         * @param {Number} months
         * @param {Number} amount
         * @returns {AFFIRM_AFFIRM.promos}
         */
        initOptions: function(apr, months, amount) {
            // payment estimate options
            this.options = {
                apr: apr,
                months: months,
                amount: amount
            };
            this.isALAInitialized = true;
            return this;
        },

         /**
          * Init limitation
          *
          * @param {String} minTotal
          * @param {String} maxTotal
          * @returns {AFFIRM_AFFIRM.promos}
         */
         initLimitation: function(minTotal, maxTotal) {
             minTotal = parseInt(minTotal);
             maxTotal = parseInt(maxTotal);
             if (!isNaN(minTotal)) {
                 this.minTotal = this.formatPriceToCents(minTotal);
             }
             if (!isNaN(maxTotal)) {
                 this.maxTotal = this.formatPriceToCents(maxTotal);
             }
             return this;
         },

        /**
         * Get options
         *
         * @returns {Object}
         */
        getOptions: function() {
            return this.options;
        },

        /**
         * Handle estimate response
         *
         * @param {Object} payment_estimate
         * @returns {AFFIRM_AFFIRM.promos}
         */
        handleEstimateResponse: function(payment_estimate) {
            // use the payment estimate response
            // the payment comes back in USD cents
            var dollars = ((payment_estimate.payment + 99) / 100) | 0; // get dollars, round up, and convert to int
            // Set affirm payment text
            var a = document.getElementById('learn-more');
            var iText = ('innerText' in a)? 'innerText' : 'textContent';
            a[iText] = "Starting at $" + dollars + " a month. Learn More";
            // open the customized Affirm learn more modal
            a.onclick = payment_estimate.open_modal;
            a.style.visibility = "visible";
            return this;
        },

         /**
          * Check threshold range
          *
          * @param {Number} amount
          */
         processPriceThresholdRange: function(amount) {
             if ((this.minTotal && amount < this.minTotal) || (this.maxTotal && amount > this.maxTotal)) {
                 var a = document.getElementById('learn-more');
                 var iText = ('innerText' in a) ? 'innerText' : 'textContent';
                 a[iText] = "";
                 a.style.visibility = "hidden";
                 return true;
             }
             return false;
         },

        /**
         * Update as low as
         *
         * @param {Number} amount
         * @param {Boolean} recalculate
         * @param {Object} container
         * @param {Object} priceFormat
         * @returns {AFFIRM_AFFIRM.promos}
         */
        updateAffirmAsLowAs: function(amount , recalculate, container, priceFormat) {
            if (!this.getIsAsLowAsInitialized()) {
                return this;
            }
            var recalculate = typeof recalculate !== 'undefined' ? recalculate : false;
            if (recalculate && (container !== null)) {
                amount = this.parsePrice(container, priceFormat)
            }

            // Only display as low as for items over $10 CHANGE FOR A DIFFERENT LIMIT
            if ((amount == null) || (amount < 1000)) {
                return this;
            }
            // Only display if price in min/max order total range
            var isProcessed = this.processPriceThresholdRange(amount);
            if (isProcessed) {
                return this;
            }

            this.options.amount = amount;
            try {
                typeof affirm.ui.payments.get_estimate; /* try and access the function */
            }
            catch (e) {
                /* stops this function - affirm functions are not loaded and will throw an error */
                return this;
            }
            // request a payment estimate
            affirm.ui.payments.get_estimate(this.options, this.handleEstimateResponse);
            return this;
        },

        /**
         * Get de bounce singleton object
         *
         * @returns {Object}
         */
        getDeBouncedAsLowAs: function () {
            if (!this.isDBInitialized) {
                this.deBounceUpdateAsLowAs = this.deBounce(this.updateAffirmAsLowAs, this.REQUEST_DELAY, false).bind(this);
                this.isDBInitialized = true;
            }
            return this.deBounceUpdateAsLowAs;
        },

         /**
          * Format price to cents
          *
          * @param {Number} amount
          * @returns {Number}
          */
         formatPriceToCents: function(amount) {
             var price;
             price = amount.toFixed(2);
             price = price.replace('.', '');
             return parseInt(price);
         },

         /**
          * Parse price to extract main price
          *
          * @param {Object} container
          * @param {Object} format
          * @returns {Number}
          */
         parsePrice: function(container, format) {
             //logic from v1.14.2.0 (compatibility reason)
             var html = container.innerHTML, amount,
                 decimalSymbol = format.decimalSymbol === undefined ? "," : format.decimalSymbol;
             var regexStr = '[^0-9-' + decimalSymbol + ']';
             //remove all characters except number and decimal symbol
             html = html.replace(new RegExp(regexStr, 'g'), '');
             html = html.replace(decimalSymbol, '.');
             amount =  parseFloat(html);
             return this.formatPriceToCents(amount);
         }
    };
    Object.extend(AFFIRM_AFFIRM.promos, asLowAs);
})(AFFIRM_AFFIRM);
