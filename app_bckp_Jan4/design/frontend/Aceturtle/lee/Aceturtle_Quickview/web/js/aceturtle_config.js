define([
    'jquery',
    'mage/mage',
    'Aceturtle_Quickview/js/jquery.magnific-popup.min'
], function ($) {
    "use strict";
    $.widget('aceturtle.aceturtle_config', {
        options: {
            productUrl: '',
            buttonText: '',
            isEnabled: false,
            baseUrl: '',
            productImageWrapper: '',
            productItemInfo: ''
        },

        _create: function () {
            this.renderButton();
            this._EventListener();
        },

        renderButton: function () {
            var $widget = this,
                id_product,
                productImageWrapper = '.' + this.options.productImageWrapper,
                productItemInfo = '.' + this.options.productItemInfo;
            if($widget.options.isEnabled == 1){
                $(productImageWrapper).each(function(){
                   
                    if ($(this).parents(productItemInfo).find('.actions-primary input[name="product"]').val() !='') {
                        id_product = $(this).parents(productItemInfo).find('.actions-primary input[name="product"]').val();
                    }
                    if (!id_product) {
                        id_product = $(this).parents(productItemInfo).find('.price-box').data('product-id');
                    }
                    if (id_product) {
                        $(this).append('<div id="quickview-'+ id_product +'" class="aceturtle-bt-quickview"><a class="aceturtle-quickview ace-tooltip tooltip-toggle" data-quickview-url="'+$widget.options.productUrl+'id/'+ id_product +'" href="javascript:void(0);" ><span class="qv-text">'+$widget.options.buttonText+'</span><span class="tooltip-content">'+$widget.options.buttonText+'</span></a></div>');
                    }
                })
            }
        },

        _EventListener: function () {
            var $widget = this;
            if($widget.options.isEnabled == 1){

                $('a.mailto').click(function(e){
                    e.preventDefault();
                    window.top.location.href = $(this).attr('href');    
                    return true;
                });

                $('body, #layer-product-list').on('contentUpdated', function () {
                    $('.aceturtle-bt-quickview').remove();
                    $widget.renderButton();
                });

                $(document).on('click','.aceturtle-quickview', function() {
                    var prodUrl = $(this).attr('data-quickview-url');
                    if (prodUrl.length) {
                        $widget.openPopup(prodUrl);
                    }
                });
            }
        },

        openPopup: function (prodUrl) {
            var $widget = this,
                url = $widget.options.baseUrl + 'aceturtle_quickview/index/updatecart';

            if (!prodUrl.length) {
                return false;
            }

            $.magnificPopup.open({
                items: {
                  src: prodUrl
                },
                type: 'iframe',
                closeOnBgClick: false,
                scrolling: false,
                preloader: true,
                tLoading: '',
                callbacks: {
                    open: function() {
                      $('.mfp-preloader').css('display', 'block');
                      $("iframe.mfp-iframe").contents().find("html").addClass("aceturtle_loader");
                    },
                    beforeClose: function() {
                        $('[data-block="minicart"]').trigger('contentLoading');
                        $.ajax({
                            url: url,
                            method: "POST"
                        });
                    },
                    close: function() {
                      $('.mfp-preloader').css('display', 'none');
                    }
                }
            });
        }
    });
    return $.aceturtle.aceturtle_config;
});
