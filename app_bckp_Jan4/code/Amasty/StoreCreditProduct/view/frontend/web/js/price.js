/**
 * Store credit pricing
 */

define([
    'jquery',
    'underscore',
    'uiComponent',
    'mage/translate'
], function ($, _, Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            prices: '',
            currencyCode: '',
            currentPrice: null,
            productId: null,
            customAmount: '',
            customMinAmount: '',
            customMaxAmount: '',
            customMinAmountCurrency: '',
            customMaxAmountCurrency: '',
            isValueValid: true,
            priceSelector: '.price',
            isSinglePrice: '',
            isLoaded: false,
            showCustomPrice: false,
            element: {
                priceLabel: '[data-amscproduct-js="price"]'
            },
            labels: {
                minLabel: $t('Min: '),
                maxLabel: $t('Max: ')
            }
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();

            this.sortPrices();
            this.setCustomAmountRangeState();
            this.showCustomPrice(!this.isSinglePrice);
            this.isLoaded(true);

            return this;
        },

        /** @inheritdoc */
        initObservable: function () {
            this._super().observe([
                'prices',
                'currentPrice',
                'isValueValid',
                'customAmount',
                'showCustomPrice',
                'isLoaded'
            ]);

            return this;
        },

        sortPrices: function () {
            this.prices().sort(function (a, b) {
                return a.value - b.value;
            });
        },

        /**
         * Change product price value
         *
         * @param {Object} item
         * @returns {void}
         */
        changeProductPrice: function (item) {
            var value = parseFloat(item.convertValue);

            this.customAmount('');
            this.applyPrice(value);
        },

        /**
         * Apply product price
         *
         * @param {Float} value
         * @returns {void}
         */
        applyPrice: function (value) {
            this.showCustomPrice(false);
            this.updatePrice(value);
        },

        /**
         * Update product price
         *
         * @param {Float} value
         * @returns {void}
         */
        updatePrice: function (value) {
            var changes = {
                    'store_credits': {
                        'finalPrice': {
                            'amount': value
                        }
                    }
                },
                selector = '#product-price-' + this.productId + ' ' + this.priceSelector;

            $(selector).trigger('updatePrice', changes);
        },

        /**
         * Get open amount range
         * @returns {String}
         */
        getAmountRange: function () {
            switch (this.customAmountRangeState) {
                case 'onlyMin':
                    return this.labels.minLabel + this.customMinAmountCurrency;
                case 'onlyMax':
                    return this.labels.maxLabel + this.customMaxAmountCurrency;
                case 'minMax':
                    return this.customMinAmountCurrency + ' - ' + this.customMaxAmountCurrency;
                default:
                    return '';
            }
        },

        isNoRestrictions: function () {
            return !parseFloat(this.customMinAmount) && !parseFloat(this.customMaxAmount);
        },

        /**
         * Set custom amount range state
         * @returns {void}
         */
        setCustomAmountRangeState: function () {
            var customMinAmount,
                customMaxAmount;

            if (this.isNoRestrictions()) {
                this.customAmountRangeState = 'noRestrictions';

                return;
            }

            customMinAmount = parseFloat(this.customMinAmount);
            customMaxAmount = parseFloat(this.customMaxAmount);

            if (customMinAmount && !customMaxAmount) {
                this.customAmountRangeState = 'onlyMin';

                return;
            }

            if (!customMinAmount && customMaxAmount) {
                this.customAmountRangeState = 'onlyMax';

                return;
            }

            this.customAmountRangeState = 'minMax';
        },

        /**
         * Set validation state
         * @param {Function} customAmount
         * @returns {void}
         */
        initCustomValidate: function (customAmount) {
            var customMinAmount = parseFloat(this.customMinAmount),
                customMaxAmount = parseFloat(this.customMaxAmount),
                validate = false;

            switch (this.customAmountRangeState) {
                case 'noRestrictions':
                    validate = true;

                    break;
                case 'onlyMin':
                    if (customMinAmount <= customAmount()) {
                        validate = true;
                    }

                    break;
                case 'onlyMax':
                    if (customMaxAmount >= customAmount()) {
                        validate = true;
                    }

                    break;
                case 'minMax':
                    if (customMaxAmount >= customAmount() && customMinAmount <= customAmount()) {
                        validate = true;
                    }

                    break;
                default:
                    validate = false;

                    break;
            }

            this.isValueValid(validate);
        },

        /**
         * Apply store sredits custom amount
         * @param {Observable} amount
         * @returns {void}
         */
        applyCustomAmount: function (amount) {
            var customAmount = amount;

            if (!this.isValueValid() || !customAmount()) {
                return;
            }

            $(this.element.priceLabel).removeClass('-active');
            customAmount = parseFloat(customAmount().replace(/,/g, '.'));

            if (typeof customAmount !== 'number') {
                return;
            }

            this.currentPrice('');
            this.applyPrice(customAmount);
        },

        /**
         * Set store credits price value
         * @param {String} name
         * @returns {void}
         */
        setCreditsPriceValue: function () {
            var price = this.prices().filter(function (el) {
                return el.convertValue === price;
            })[0]; // IE compatibility

            if (!_.isUndefined(price)) {
                this.currentPrice(price.value);
            }
        },

        /**
         * Is need to show price selector
         * @returns {Boolean}
         */
        showPriceSelector: function () {
            var priceOptionsLength = this.prices().length;

            return priceOptionsLength > 1
                || (priceOptionsLength === 1 && this.isOpenAmount);
        }
    });
});
