( function ( $, pageNow ) {
    /* OWL CAROUSEL */
    $(function () {
        var dragging = true;
        var owlElementID = "#owl-main";
        
        function fadeInReset() {
            if (!dragging) {
                jQuery(owlElementID + " .caption .fadeIn-1, " + owlElementID + " .caption .fadeIn-2, " + owlElementID + " .caption .fadeIn-3").stop().delay(800).animate({ opacity: 0 }, { duration: 400, easing: "easeInCubic" });
            }
            else {
                jQuery(owlElementID + " .caption .fadeIn-1, " + owlElementID + " .caption .fadeIn-2, " + owlElementID + " .caption .fadeIn-3").css({ opacity: 0 });
            }
        }
        
        function fadeInDownReset() {
            if (!dragging) {
                jQuery(owlElementID + " .caption .fadeInDown-1, " + owlElementID + " .caption .fadeInDown-2, " + owlElementID + " .caption .fadeInDown-3").stop().delay(800).animate({ opacity: 0, top: "-15px" }, { duration: 400, easing: "easeInCubic" });
            }
            else {
                jQuery(owlElementID + " .caption .fadeInDown-1, " + owlElementID + " .caption .fadeInDown-2, " + owlElementID + " .caption .fadeInDown-3").css({ opacity: 0, top: "-15px" });
            }
        }
        
        function fadeInUpReset() {
            if (!dragging) {
                jQuery(owlElementID + " .caption .fadeInUp-1, " + owlElementID + " .caption .fadeInUp-2, " + owlElementID + " .caption .fadeInUp-3").stop().delay(800).animate({ opacity: 0, top: "15px" }, { duration: 400, easing: "easeInCubic" });
            }
            else {
                $(owlElementID + " .caption .fadeInUp-1, " + owlElementID + " .caption .fadeInUp-2, " + owlElementID + " .caption .fadeInUp-3").css({ opacity: 0, top: "15px" });
            }
        }
        
        function fadeInLeftReset() {
            if (!dragging) {
                jQuery(owlElementID + " .caption .fadeInLeft-1, " + owlElementID + " .caption .fadeInLeft-2, " + owlElementID + " .caption .fadeInLeft-3").stop().delay(800).animate({ opacity: 0, left: "15px" }, { duration: 400, easing: "easeInCubic" });
            }
            else {
                jQuery(owlElementID + " .caption .fadeInLeft-1, " + owlElementID + " .caption .fadeInLeft-2, " + owlElementID + " .caption .fadeInLeft-3").css({ opacity: 0, left: "15px" });
            }
        }
        
        function fadeInRightReset() {
            if (!dragging) {
                jQuery(owlElementID + " .caption .fadeInRight-1, " + owlElementID + " .caption .fadeInRight-2, " + owlElementID + " .caption .fadeInRight-3").stop().delay(800).animate({ opacity: 0, left: "-15px" }, { duration: 400, easing: "easeInCubic" });
            }
            else {
                jQuery(owlElementID + " .caption .fadeInRight-1, " + owlElementID + " .caption .fadeInRight-2, " + owlElementID + " .caption .fadeInRight-3").css({ opacity: 0, left: "-15px" });
            }
        }
        
        function fadeIn() {
            jQuery(owlElementID + " .active .caption .fadeIn-1").stop().delay(500).animate({ opacity: 1 }, { duration: 800, easing: "easeOutCubic" });
            jQuery(owlElementID + " .active .caption .fadeIn-2").stop().delay(700).animate({ opacity: 1 }, { duration: 800, easing: "easeOutCubic" });
            jQuery(owlElementID + " .active .caption .fadeIn-3").stop().delay(1000).animate({ opacity: 1 }, { duration: 800, easing: "easeOutCubic" });
        }
        
        function fadeInDown() {
            jQuery(owlElementID + " .active .caption .fadeInDown-1").stop().delay(500).animate({ opacity: 1, top: "0" }, { duration: 800, easing: "easeOutCubic" });
            jQuery(owlElementID + " .active .caption .fadeInDown-2").stop().delay(700).animate({ opacity: 1, top: "0" }, { duration: 800, easing: "easeOutCubic" });
            jQuery(owlElementID + " .active .caption .fadeInDown-3").stop().delay(1000).animate({ opacity: 1, top: "0" }, { duration: 800, easing: "easeOutCubic" });
        }
        
        function fadeInUp() {
            jQuery(owlElementID + " .active .caption .fadeInUp-1").stop().delay(500).animate({ opacity: 1, top: "0" }, { duration: 800, easing: "easeOutCubic" });
            jQuery(owlElementID + " .active .caption .fadeInUp-2").stop().delay(700).animate({ opacity: 1, top: "0" }, { duration: 800, easing: "easeOutCubic" });
            jQuery(owlElementID + " .active .caption .fadeInUp-3").stop().delay(1000).animate({ opacity: 1, top: "0" }, { duration: 800, easing: "easeOutCubic" });
        }
        
        function fadeInLeft() {
            jQuery(owlElementID + " .active .caption .fadeInLeft-1").stop().delay(500).animate({ opacity: 1, left: "0" }, { duration: 800, easing: "easeOutCubic" });
            jQuery(owlElementID + " .active .caption .fadeInLeft-2").stop().delay(700).animate({ opacity: 1, left: "0" }, { duration: 800, easing: "easeOutCubic" });
            jQuery(owlElementID + " .active .caption .fadeInLeft-3").stop().delay(1000).animate({ opacity: 1, left: "0" }, { duration: 800, easing: "easeOutCubic" });
        }
        
        function fadeInRight() {
            jQuery(owlElementID + " .active .caption .fadeInRight-1").stop().delay(500).animate({ opacity: 1, left: "0" }, { duration: 800, easing: "easeOutCubic" });
            jQuery(owlElementID + " .active .caption .fadeInRight-2").stop().delay(700).animate({ opacity: 1, left: "0" }, { duration: 800, easing: "easeOutCubic" });
            jQuery(owlElementID + " .active .caption .fadeInRight-3").stop().delay(1000).animate({ opacity: 1, left: "0" }, { duration: 800, easing: "easeOutCubic" });
        }
        
        jQuery(owlElementID).owlCarousel({
            
            autoPlay: 5000,
            stopOnHover: true,
            navigation: true,
            pagination: true,
            singleItem: true,
            addClassActive: true,
            transitionStyle: "fade",
            navigationText: ["<i class='icon fa fa-angle-left'></i>", "<i class='icon fa fa-angle-right'></i>"],
            
            afterInit: function() {
                fadeIn();
                fadeInDown();
                fadeInUp();
                fadeInLeft();
                fadeInRight();
            },
            
            afterMove: function() {
                fadeIn();
                fadeInDown();
                fadeInUp();
                fadeInLeft();
                fadeInRight();
            },
            
            afterUpdate: function() {
                fadeIn();
                fadeInDown();
                fadeInUp();
                fadeInLeft();
                fadeInRight();
            },
            
            startDragging: function() {
                dragging = true;
            },
            
            afterAction: function() {
                fadeInReset();
                fadeInDownReset();
                fadeInUpReset();
                fadeInLeftReset();
                fadeInRightReset();
                dragging = false;
            }
            
        });
        
        if (jQuery(owlElementID).hasClass("owl-one-item")) {
            jQuery(owlElementID + ".owl-one-item").data('owlCarousel').destroy();
        }
        
        jQuery(owlElementID + ".owl-one-item").owlCarousel({
            singleItem: true,
            navigation: false,
            pagination: false
        });
        
        
        
        
        jQuery('.home-owl-carousel').each(function(){
            
            var owl = $(this);
            var  itemPerLine = owl.data('item');
            if(!itemPerLine){
                itemPerLine = 8;
            }
            owl.owlCarousel({
                items : itemPerLine,
                itemsDesktop : [1199,3],
                itemsTablet:[991,2],
                navigation : true,
                pagination : false,
                
                navigationText: ["", ""]
            });
        });
        
        jQuery('.homepage-owl-carousel').each(function(){
            
            var owl = $(this);
            var  itemPerLine = owl.data('item');
            if(!itemPerLine){
                itemPerLine = 4;
            }
            owl.owlCarousel({
                items : itemPerLine,
                itemsTablet:[991,2],
                itemsDesktop : [1199,3],
                navigation : true,
                pagination : false,
                
                navigationText: ["", ""]
            });
        });
        
        jQuery(".blog-slider").owlCarousel({
            items : 3,
            itemsDesktopSmall :[979,2],
            itemsDesktop : [1199,3],
            navigation : true,
            slideSpeed : 300,
            pagination: false,
            navigationText: ["", ""]
        });
        
        jQuery(".best-seller").owlCarousel({
            items : 3,
            navigation : true,
            itemsDesktopSmall :[979,2],
            itemsDesktop : [1199,2],
            slideSpeed : 300,
            pagination: false,
            paginationSpeed : 400,
            navigationText: ["", ""]
        });
        
        jQuery(".sidebar-carousel").owlCarousel({
            items : 1,
            itemsTablet:[978,1],
            itemsDesktopSmall :[979,2],
            itemsDesktop : [1199,1],
            navigation : true,
            slideSpeed : 300,
            pagination: false,
            paginationSpeed : 400,
            navigationText: ["", ""]
        });
        
        jQuery(".brand-slider").owlCarousel({
            items :6,
            navigation : true,
            slideSpeed : 300,
            pagination: false,
            paginationSpeed : 400,
            navigationText: ["", ""]
        });
        jQuery("#advertisement").owlCarousel({
            items : 1,
            itemsTablet:[978,1],
            itemsDesktopSmall :[979,1],
            itemsDesktop : [1199,1],
            navigation : true,
            slideSpeed : 300,
            pagination: true,
            paginationSpeed : 400,
            navigationText: ["", ""]
        });
    });
    /* SINGLE PRODUCT GALLERY */
    $('#owl-single-product').owlCarousel({
        items:1,
        itemsTablet:[768,3],
        itemsDesktop : [1199,1],
        itemsDesktopSmall : [768,3]
        
    });
    $('#owl-single-product-thumbnails').owlCarousel({
        items: 4,
        pagination: true,
        rewindNav: true,
        itemsTablet : [992,4],
        itemsDesktopSmall :[768,4],
        itemsDesktop : [992,1]
    });
    $('#owl-single-product2-thumbnails').owlCarousel({
        items: 6,
        pagination: true,
        rewindNav: true,
        itemsTablet : [768, 4],
        itemsDesktop : [1199,3]
    });
    $('.single-product-slider').owlCarousel({
        stopOnHover: true,
        rewindNav: true,
        singleItem: true,
        pagination: true
    });
} )( jQuery, pageNow )
