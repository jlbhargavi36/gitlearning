/**
 * Store Credit Product Edit on Order Create
 */
define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('amasty.scProduct', {
        options: {
            selectors: {
                hideClass: 'amscproduct-hide',
                validateClass: 'required-entry',
                fieldSelector: '[data-amscproduct-js="field"]',
                amountSelector: '[data-amscproduct-js="amount"]',
                customAmountSelector: '[data-amscproduct-js="custom-amount"]'
            }
        },

        /** @inheritdoc */
        _create: function () {
            this.initHandlers();
        },

        initHandlers: function () {
            var selectors = this.options.selectors;

            $(selectors.amountSelector).on('change', this.toggleCustomAmount.bind(this));
            $(selectors.fieldSelector).on('blur', function () {
                this.setAttribute('price', this.value);
            });
        },

        /**
         * @param {String} elementSelector
         * @returns {void}
         */
        toggleState: function (elementSelector) {
            $(elementSelector)
                .toggleClass(this.options.selectors.hideClass)
                .find(this.options.selectors.fieldSelector)
                .toggleClass(this.options.selectors.validateClass);
        },

        toggleCustomAmount: function () {
            var selectors = this.options.selectors,
                isCustomAmountEnable = $(selectors.amountSelector).val() === 'custom',
                isCustomAmountHide = $(selectors.customAmountSelector).hasClass(selectors.hideClass);

            if ((isCustomAmountEnable && isCustomAmountHide) || (!isCustomAmountEnable && !isCustomAmountHide)) {
                this.toggleState(selectors.customAmountSelector);
            }
        }
    });

    return $.amasty.scProduct;
});
