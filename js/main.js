jQuery(document).ready(function ($) {
    var swiper = new Swiper({
        el: '.swiper-container',
        initialSlide: 2,
        spaceBetween: 10,
        slidesPerView: swipeparams.slidesperview,
        centeredSlides: true,
        slideToClickedSlide: true,
        effect: swipeparams.effect,
        

        scrollbar: {
          el: '.swiper-scrollbar',
        },
        mousewheel: {
          enabled: true,
        },
        keyboard: {
          enabled: true,
        },

        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
    });

    $(".snillrik_calenderbox").on("click",function(){
        let url = $(this).find(".calendar_url").attr("href");
        //console.log(url);
        window.location = url;
    });
});