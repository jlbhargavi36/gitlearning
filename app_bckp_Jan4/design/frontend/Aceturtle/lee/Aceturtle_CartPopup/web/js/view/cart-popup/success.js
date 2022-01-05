define([
    'uiComponent',
    'ko',
    'jquery',
    'mage/translate',
    'Aceturtle_CartPopup/js/model/cart-popup',
    'Magento_Customer/js/customer-data',
    'Magento_Catalog/js/price-utils'
], function(Component, ko, $, $t, cartPopupModel, customerData, priceUtils) {
    return Component.extend({
        defaults: {
            template: 'Aceturtle_CartPopup/cart-popup/success',
            priceFormat: null,
            cartUrl: ''
        },
        addedItem: cartPopupModel.addedItem,
        relatedProductsBlock: cartPopupModel.relatedProductsBlock,
        swatchBlock: cartPopupModel.swatchBlock,

        title: ko.computed(function() {
            var cartData = customerData.get('cart');

            return $t('<b> Successfully added to your cart </b>');
            //return $t('<b> ITEMS IN YOUR BAG: %1 </b>')
            //    .replace('%1', (cartData().summary_count || '...'));
        }),
        // totol in plp and plp of items
        subtotal: ko.computed(function() {
                    var cartData = customerData.get('cart');

                    return $t('<p>Total <span class="cart-popup-total"> %1 </span></p>')
                        .replace('%1', (cartData().subtotal || '...'));
        }),

        continueShopping: function () {
            cartPopupModel.closeModal();
        },
        movecartUrl: function () {
            window.location.href = this.cartUrl;
        },

        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, this.priceFormat);
        }
    });
});

