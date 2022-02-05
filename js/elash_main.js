$(document).ready(function() {  
    $('.slider').slick({
        dots: true,
        infinite: true,
        arrows: true,
        slidesToShow: $('.slider').data('slides'),
        autoplay: ($('.slider').data('play') == '1') ? true : false,
        slidesToScroll: $('.slider').data('scroll'),
        autoplaySpeed: $('.slider').data('speed'),
    });
});