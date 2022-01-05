define([
    'uiComponent',
    'ko',
    'jquery',
    'mage/translate',
    'Aceturtle_CartPopup/js/model/cart-popup'
], function(Component, ko, $, $t, cartPopupModel) {
    'use strict';

    // @TODO add jsDoc

    return Component.extend({
        defaults: {
            template: 'Aceturtle_CartPopup/cart-popup',
            priceFormat: null,
            cartUrl: ''
        },
        modalWindow: null,

        // @TODO add support for multiple items, for checkboxes related products in pdp for example
        addedItems: cartPopupModel.addedItems,
        optionsBlock: cartPopupModel.optionsBlock,

        initialize: function() {
            this._super();
        },

        setModalElement: function (element) {
            var cartUrl = this.cartUrl;

            if (cartPopupModel.modalWindow == null) {
                cartPopupModel.createPopUp(element, {
                    // @TODO refactor title
                    title: $t('Loading...'),
                    modalTitle: '.cart-popup [data-role="title"]',

                    buttons: [{
                        text: $t('CONTINUE SHOPPING'),
                        class: 'action-secondary action-save'
                    }, {
                        text: $t('VIEW BAG & CHECKOUT'),
                        class: 'action-primary action-save',
                        click: function() {
                            window.location = cartUrl
                        }
                    }]
                });
            }
        },

        closeModal: function (uiClass, e) {
            e.preventDefault();
            cartPopupModel.closeModal();
        },

        triggerContentUpdated: function (element) {
            $(element).trigger('contentUpdated');
        }
    });
});
