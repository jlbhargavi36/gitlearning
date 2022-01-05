define([
    'jquery',
    'Aceturtle_Quickview/js/aceturtle_tocart',
    'mage/mage',
    'Magento_Catalog/product/view/validation',
    'Magento_Catalog/js/catalog-add-to-cart'
], function ($) {
    'use strict';

    $.widget('aceturtle.aceturtle_tocart', {
        _create: function () {
            'use strict';
            $('#product_addtocart_form').mage('validation', {
                radioCheckboxClosest: '.nested',
                submitHandler: function (form) {
                    var widget = $(form).catalogAddToCart({
                        bindSubmit: false
                    });
                    widget.catalogAddToCart('submitForm', $(form));
                    return false;
                }
            });
            $('#ajax-goto a').click(function(e){
                e.preventDefault();
                window.top.location.href = $(this).attr('href');
                
                return false;
            });
        }
    });
    return $.aceturtle.aceturtle_tocart;
});
