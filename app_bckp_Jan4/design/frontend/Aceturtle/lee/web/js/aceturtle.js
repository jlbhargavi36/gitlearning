require(['jquery'], function(jQuery) {
    jQuery.migrateMute = true;
});
 
require(['jquery', 'matchMedia', 'js/owl.carousel'], function ($, mediaCheck) {
    
    var ismobile = false,  homepage  = $('.cms-home .home-page');
    changeBg();
    mediaCheck({
        media: '(max-width: 768px)',
        entry: $.proxy(function () {
            ismobile = true;
            //console.log('it is mobile');
            $("[data-responsive-bg-img]").trigger('changedImg');
            $('.mob-pdp-title').prepend($('h1.page-title'));
            $('.fix-soical').prepend($('.product-addto-links'));
        }, this),
        exit: $.proxy(function () {
            ismobile = false;
            $("[data-responsive-bg-img]").trigger('changedImg');
            $('.page-title-wrapper.product').prepend($('h1.page-title'));
            $('.product-social-links').prepend($('.product-addto-links'));
           // console.log('not mobile');
        }, this)
    });

    mediaCheck({
        media: '(max-width: 1023px)',
        entry: $.proxy(function () {
           $('nav.navigation').prepend($('#desk-menu > ul'));
        }, this),
        exit: $.proxy(function () {
           $('#desk-menu').prepend($('nav.navigation > ul'));
        }, this)
    });

    /***   for footer collapsible   ***/
    $('.footer-item.collapsible-xs').on('click', 'a.title', function (e) {
        e.preventDefault();
        if (ismobile) {
            var parent = $(this).parents('.footer-item.collapsible-xs');
            if (parent.hasClass('active')) {
                parent.removeClass('active');
            } else {
                parent.addClass('active');
            }
        }
    });
    function changeBg(){ 
        $("[data-responsive-bg-img]").each(function(){
            var elem = $(this);
            bgImg(elem);
            elem.on('changedImg', function() {
                var elem = $(this);
                bgImg(elem); 
            });
        });
        
    }

    function bgImg(ele){
        var elem = ele, item = JSON.parse(elem.attr('data-responsive-bg-img')), sm = item.small, med = item.medium;
        if(ismobile && sm){
            imgUrl = sm;
        } else {
            imgUrl = med;
        }
        // console.log('imgUrl', imgUrl);
        elem.css("background-image", "url(" + imgUrl + ")");
    }

    // header fix in mobile

    $(window).scroll(function () {

        //wishlist seleted
        jQuery(".towishlist").click(function(){
            jQuery(this).addClass("wishlist-selected");
        });


       if ($(this).scrollTop() > 50) {
           $('body').addClass('fixed');
       } 
       else {
           $('body').removeClass('fixed');
       }
    });
    $('button.close').on('click', function(){
        $(this).parents('.slide-up').hide();
    })
    $(document).ready(function(){
        // Banner next testimonials
        $ ('#Banner_next_testi_images').owlCarousel({
            autoplay: true,
            loop: true,
            autoHeight:true,
            items: 1,
            slideBy: 1,
            navigation: true,
            pagination: true,
            touchDrag  : true,
            mouseDrag  : true,
        });

        // Hero banner slider 
        var heroBanner = setInterval(function () {
            if ($('#slideshow-hero-banner').length) {
                clearInterval(heroBanner);
                $ ('#slideshow-hero-banner').owlCarousel({
                    autoplay: true,
                    loop: true,
                    autoHeight:true,
                    items: 1,
                    slideBy: 1,
                    navigation: true,
                    pagination: true,
                    touchDrag  : true,
                    mouseDrag  : true,
                });
            }

            if ($('#slideshow-hero-banner-mobile').length) {
                clearInterval(heroBanner);
                $ ('#slideshow-hero-banner-mobile').owlCarousel({
                    autoplay: true,
                    loop: true,
                    items: 1,
                    slideBy: 1,
                    navigation: true,
                    pagination: true,
                    touchDrag  : true,
                    mouseDrag  : true,
                });
            }
            
        }, 50);

    // All slider
    var homeclassName = $('body').hasClass('cms-home');
    if(homeclassName==true){ 
        var prodSlider = setInterval(function () {
            if ($('.products-slider').length) {
                var items = 4,
                margin = 26;
                if($('.products-slider.new-products-slider').length){
                    items = 4;
                    margin = 26;
                }
                clearInterval(prodSlider);
                $('.products-slider .product-items').owlCarousel({
                    margin: margin,
                    loop:true,
                    navRewind: false,
                    items: items,
                    slideBy: 3,
                    navigation : true,
                    autoplay: true,
                    pagination : false,
                    responsiveClass:true,
                    dotsEach: true,
                    nav: false,
                    responsive:{
                        0:{
                            items:1,
                            margin: 20,
                            loop:true
                        },
                        650:{
                            items:2,
                            margin: 20,
                            loop:true
                        },
                        900:{
                            items:3,
                            margin: 20,
                            loop:true
                        },
                    
                        1200:{
                            items:items,
                            loop:true
                        }
                    }
                });

                $('.owl-carousel').on('changed.owl.carousel', function(e) {
                    $('button.owl-next').removeAttr('disabled');
                    $('button.owl-prev').removeAttr('disabled');

                    if ( ( e.page.index + 1 ) >= e.page.count ){
                        $('button.owl-next').attr('disabled', 'disabled');
                    } else {
                        $('button.owl-next').removeAttr('disabled');
                    }
                    
                    if ( e.page.index == 0 ){
                        $('button.owl-prev').attr('disabled', 'disabled');
                    } else {
                        $('button.owl-prev').removeAttr('disabled');
                    }
                });
            }
        }, 5); 
    }    
    });
});

require(['jquery'],function($){
    $(document).ready(function(){

        $('.dropdown-main-account-label').click(function(event){
            if($(".dropdown-main-account-label").hasClass("active")){
                $(".dropdown-main-account-label").removeClass("active");
            }else{
                $(".dropdown-main-account-label").addClass("active");
            }
            $('.sub-dropdown-account').toggle();
            event.preventDefault();
        });
          
      $('.chatbot').on('click', '.track-icon', function() {
          $(this).closest('.chatbot').find('.chat-container').show();
          $(this).parent().hide();
      });
      $('.chatbot').on('click','.track-back-button, .chat-cross', function() {
          $(this).closest('.chat-container').hide();
          $(this).closest('.chatbot').find('.chat-icon').show();
      });
 
        $('.passWordToggle').click(function(){
             
                if('password' == $('#pass').attr('type')){
                     $('#pass').prop('type', 'text');
                }else{
                     $('#pass').prop('type', 'password');
                }
            
        });
        if ($(window).width() < 700){
            $('.mobile').show(); 
            $('.desktop').remove();         
        }
        else {    
            $('.desktop').show(); 
            $('.mobile').remove();     
        }
    }); 
    $(window).scroll(function () {
        if ($(window).scrollTop() >= 550) {
  
            $('#maintimeLine').addClass('fixed');

        } 
        else {
            $('#maintimeLine').removeClass('fixed');
        }
  
         if($(window).scrollTop() + $(window).height() > $(document).height() - 400) {
            $('#maintimeLine').removeClass('fixed');     
         } 

    });
 

    $(document).ready(function() {
        
        // Size chart
        jQuery(".product-sizechart-head a").on('click', function () {
            jQuery(".product-sizechart-head a").removeClass('chartactive');
            jQuery(this).addClass("chartactive");   

        });

        //wishlist seleted

        jQuery(".towishlist").click(function(){
            jQuery(this).addClass("wishlist-selected");
        });

        jQuery(".minicart-wrapper .showcart").on('click', function (e) {
            $('.dropdown-main-account-label').removeClass("active"); 
             $('.sub-dropdown-account').css('display','none'); 
    
         }); 

        jQuery(".minicart-wrapper").on('click', function (e) {
            $(".minicart-items .cart-item-qty").keypress(function (e) {
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                        return false;
                }
            });    
        }); 

        $('body').click(function(event) {
            if (!$('.dropdown-main-account-label').is(event.target) && $('.dropdown-main-account-label').has(event.target).length === 0) {
                $('.dropdown-main-account-label').removeClass('active');
                $('.sub-dropdown-account').hide();
            }
        });
        // mini cart 

        $(".minicart-wrapper ,.minicart-wrapper a").on('click', function (e) {
            $(".minicart-items .cart-item-qty").keypress(function (e) {
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                        return false;
                }
            });    
        });
        $('img.thumbnail').click(function() {
            window.location.href = this.id + '.html';
        });
         
    });
});