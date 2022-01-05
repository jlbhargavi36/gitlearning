define(['jquery'], function ($) {
    function lazyload() {
        $('[data-src]').each(function() {
            if (($(window).scrollTop() + window.innerHeight) >= $(this).offset().top) {
                $(this).attr('src', $(this).data('src')).removeAttr('data-src');
            }
        })
    }
    $(document).ready(function (){
        lazyload();
    });
    $(window).on('scroll', lazyload);
    $('img').on('mouseover', lazyload);
});