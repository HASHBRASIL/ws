$(document).ready(function(){
    $('.mobile-button').click(function(){
        $('.navbar-collapse').slideToggle('slow');
    });

    $('.carousel-top-content').slick({
        dots: true,
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true
    });

    $('.carousel-content').slick({
        dots: true,
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false
    });

    $(window).bind("scroll", function () {
        if (this.pageYOffset >= 60) {
            $('#landing-header').addClass('min');
        } else {
            $('#landing-header').removeClass('min');
        }
    });

})
