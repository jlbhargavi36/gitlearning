/**
 * Store credits product view
 */

define([
    'uiComponent',
    'jquery'
], function (Component, $) {
    'use strict';

    return Component.extend({
        defaults: {
            selectors: {
                addToCardButton: '#product-addtocart-button',
                form: '#product_addtocart_form'
            }
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();

            $(this.selectors.form).attr('enctype', 'multipart/form-data'); // for update cart
            $(this.selectors.addToCardButton).on('click', this.validateForm.bind(this));

            return this;
        },

        /**
         * @param {Event} event
         * @returns {void}
         */
        validateForm: function (event) {
            if ($(this.selectors.form).validate().form()) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
        }
    });
});
