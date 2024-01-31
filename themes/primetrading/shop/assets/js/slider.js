( function ( $, window, document, site, cart, lang, restoreHome, sys_alerts, filters, viewProduct, pageNow ) {
    // $('.brand-slider').each(function () {
    //     var $slider = $(this);
    //     $slider.on("initialized.owl.carousel2", function () {
    //         setTimeout(function() {
    //             $slider.parent().find('.loading-placeholder').hide();
    //         }, 1000);
    //     });
    // });
    var total_item = $(".ui-banner-slider .item").length;
    var navText = [ '<span class="sr-only">Prev</span>', '<span class="sr-only">Next</span>' ],
        navText2 = ["&#171", "&#187"],
        sliderCommon = {
            rtl: $('html').attr('dir') === 'rtl',
            margin: 30,
            slideBy: 1,
            loop: true,
            autoplay: false,
            video: true,
            autoplayHoverPause: false,
            autoplaySpeed: 1000,
            dotsSpeed: 500,
            nav: true,
            lazyLoad: true,
            navSpeed: 500,
            navText: navText2,
        };
    $(".ui-banner-slider").owlCarousel2( $.extend( {}, sliderCommon, {
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        autoplay: true,
        smartSpeed: 500,
        autoplayHoverPause: true,
        loop: true,
        navText: navText,
        responsive: {
            0:{ items: 1, nav: total_item > 1, navText: navText },
            480:{ items: 1, nav: total_item > 1, navText: navText },
            768:{ items: 1, nav: total_item > 1, navText: navText },
            992:{ items: 1, nav: total_item > 1, navText: navText },
            1200:{ items: 1, nav: total_item > 1, navText: navText }
        },
    } ));
    $('.home-owl-carousel').not('.home-tab-slider').each(function(){
        var owl = $(this),
            homeSectionSliders = $.extend( {}, sliderCommon, {
                autoplayTimeout: 0 ,
                navText: [ '', '' ],
                responsive: {
                    0: 	{ items: 1, nav: true, navText: navText } ,
                    480: { items: 2, nav: true, navText: navText },
                    768: { items: 2, nav: true, navText: navText },
                    992: { items: 3, nav: true, navText: navText },
                    1200: { items: owl.data('item') || 8, nav: true, navText: navText },
                },
            } );
        owl.owlCarousel2( homeSectionSliders );
    });
    $('.brand-slider').owlCarousel2( $.extend( {}, sliderCommon, {
        responsiveClass: true,
        video: false,
        autoplay: true,
        autoplayTimeout: 4000,
        smartSpeed: 600,
        autoplayHoverPause: true,
        loop: true,
        dots: false,
        nav: true,
        margin: 0,
        navText: ['',''],
        responsive: {
            0: { items   : 1, },
            480: { items   : 2, },
            768: { items   : 3 },
            992: { items   : 6 },
            1200: { items   : 8 }
        }
    } ) );
    $(".sidebar-slider").owlCarousel2(  $.extend( {}, sliderCommon, {
        margin: 0,
        autoplayTimeout: 0 ,
        nav: false,
        loop: true,
        responsive: {
            0:      { items: 1 } ,
            480:    { items: 1 },
            768:    { items: 1 },
            992:    { items: 1 },
            1200:   { items: 1 }
        },
    } ));
    $('.sidebar-slider-inner').owlCarousel2( $.extend( {}, sliderCommon, {
        responsive: {
            0: 	{ items: 1 } ,
            480: { items: 1 },
            768: { items: 1 },
            992: { items: 2 },
            1200: {items: 2 }
        },
    } ) );
    $('.most-viewed-carousel').owlCarousel2( $.extend( {}, sliderCommon, {
        dots: true,
        loop: true,
        responsive: {
            0: 	{ items: 1 } ,
            480: { items: 1 },
            768: { items: 2 },
            992: { items: 3 },
            1200: {items: 4}
        },
    } ) );
    $('[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var el = $(this),
            target = $( el.attr('href') ),
            tabSlider = target.find( '.home-tab-slider');
        if ( tabSlider.length && ! tabSlider.hasClass( 'loaded' ) ) {
            
            var homeSectionSliders = $.extend( {}, sliderCommon, {
                autoplayTimeout: 0 ,
                navText: [ '', '' ],
                responsive: {
                    0: 	{ items: 1, nav: true, navText: navText } ,
                    480: { items: 2, nav: true, navText: navText },
                    768: { items: 2, nav: true, navText: navText },
                    992: { items: 3, nav: true, navText: navText },
                    1200: { items: tabSlider.data('item') || 8, nav: true, navText: navText },
                },
            } );
            
            tabSlider.owlCarousel2( homeSectionSliders );
            tabSlider.addClass('loaded');
        }
    });
})( jQuery, window, document, site, cart, lang, restoreHome, sys_alerts, filters, viewProduct, pageNow );
