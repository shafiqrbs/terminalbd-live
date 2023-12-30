/*======================== Page Slider ============================*/

var owlFeatureSidebarSLider = $("#feature-mobile-slider");
owlFeatureSidebarSLider.owlCarousel({
    items:1,
    slideSpeed : 3000,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    singleItem:true,
    transitionStyle:"goDown",
    rewindNav : false,
    autoPlay:false,
    navigation:true,
    navigationText: [
        "<a id='feature-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
        "<a id='feature-page-next' class='glyphicon glyphicon-chevron-right'></a>"
    ],
});

/*======================== End Page Slider =============================*/


/*======================== Blog Slider =============================*/

var owlSidebarBlog = $("#blog-mobile-slider");
owlSidebarBlog.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    slideSpeed:3000,
    singleItem:true,
    transitionStyle:"goDown",
    rewindNav : false,
    autoPlay:false,
    navigation:true,
    navigationText: [
        "<a id='blog-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
        "<a id='blog-page-next' class='glyphicon glyphicon-chevron-right'></a>"
    ],
});


/*======================== End Blog Slider =============================*/

/*======================== Branch Slider =============================*/

var owlSidebarBranch = $("#branch-mobile-slider");
owlSidebarBranch.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    slideSpeed:3000,
    singleItem:true,
    transitionStyle:"goDown",
    rewindNav : false,
    autoPlay:false,
    navigation:true,
    navigationText: [
        "<a id='branch-sidebar-prev' class='glyphicon glyphicon-chevron-left'></a>",
        "<a id='branch-sidebar-next' class='glyphicon glyphicon-chevron-right'></a>"
    ],
});

/*======================== End Branch Slider =============================*/

/*======================== Client Slider =============================*/

var owlSidebarClient = $("#client-mobile-slider");
owlSidebarClient.owlCarousel({
    items:2,
    slideSpeed:3000,
    itemsDesktop:[1199,2],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,2],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false,
    navigation : true,
    navigationText: [
        "<a id='client-sidebar-prev' class='glyphicon glyphicon-chevron-left'></a>",
        "<a id='client-sidebar-next' class='glyphicon glyphicon-chevron-right'></a>"
    ],
});

/*======================== End Client Slider =============================*/


/*======================== Event Slider =============================*/

var owlSidebarEvent = $("#event-mobile-slider");
owlSidebarEvent.owlCarousel({
    items:1,
    slideSpeed:3000,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    singleItem:true,
    transitionStyle:"goDown",
    autoPlay:false,
    navigation:true,
    navigationText: [
        "<a id='event-sidebar-prev' class='glyphicon glyphicon-chevron-left'></a>",
        "<a id='event-sidebar-next' class='glyphicon glyphicon-chevron-right'></a>"
    ],
});

/*======================== News Slider =============================*/

var owlSidebarNews = $("#news-mobile-slider");
owlSidebarNews.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    slideSpeed:3000,
    singleItem:true,
    transitionStyle:"backSlide",
    autoPlay:false,
    rewindNav : false,
    navigation:true,
    navigationText: [
        "<a id='news-sidebar-prev' class='glyphicon glyphicon-chevron-left'></a>",
        "<a id='news-sidebar-next' class='glyphicon glyphicon-chevron-right'></a>"
    ],

});



/*======================== Notice Slider =============================*/

var owlSidebarNotice = $("#notice-mobile-slider");
owlSidebarNotice.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    slideSpeed:3000,
    singleItem:true,
    transitionStyle:"backSlide",
    autoPlay:false,
    rewindNav : false,
    navigation:true,
    navigationText: [
        "<a id='notice-sidebar-prev' class='glyphicon glyphicon-chevron-left'></a>",
        "<a id='notice-sidebar-next' class='glyphicon glyphicon-chevron-right'></a>"
    ],

});

/*======================== Portfolio Slider =============================*/

var owlSidebarPortfolio = $("#portfolio-mobile-slider");
owlSidebarPortfolio.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    slideSpeed:3000,
    singleItem:true,
    transitionStyle:"backSlide",
    autoPlay:false,
    rewindNav : false,
    navigation:true,
    navigationText: [
        "<a id='portfolio-sidebar-prev' class='glyphicon glyphicon-chevron-left'></a>",
        "<a id='portfolio-sidebar-next' class='glyphicon glyphicon-chevron-right'></a>"
    ],

});

/*======================== Service Slider =============================*/

var owlSidebarService = $("#service-mobile-slider");
owlSidebarService.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    slideSpeed:3000,
    singleItem:true,
    transitionStyle:"goDown",
    autoPlay:false,
    rewindNav : false,
    navigation:true,
    navigationText: [
        "<a id='service-sidebar-prev' class='glyphicon glyphicon-chevron-left'></a>",
        "<a id='service-sidebar-next' class='glyphicon glyphicon-chevron-right'></a>"
    ],

});

/*======================== Sponsor Slider =============================*/

var owlSidebarSponsor = $("#sponsor-mobile-slider");
owlSidebarSponsor.owlCarousel({
    items:2,
    itemsDesktop:[1199,2],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,2],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false,
    navigation:true,
    navigationText: [
        "<a id='sponsor-sidebar-prev' class='glyphicon glyphicon-chevron-left'></a>",
        "<a id='sponsor-sidebar-next' class='glyphicon glyphicon-chevron-right'></a>"
    ],


});

/*======================== Team Slider =============================*/

var owlSidebarTeam = $("#team-mobile-slider");
owlSidebarTeam.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    slideSpeed:3000,
    singleItem:true,
    transitionStyle:"goDown",
    autoPlay:false,
    rewindNav : false,
    navigation:true,
    navigationText: [
        "<a id='team-sidebar-prev' class='glyphicon glyphicon-chevron-left'></a>",
        "<a id='team-sidebar-next' class='glyphicon glyphicon-chevron-right'></a>"
    ],

});

/*======================== Testimonial Slider =============================*/

var owlSidebarTestimonial = $("#testimonial-mobile-slider");
owlSidebarTestimonial.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    slideSpeed:3000,
    singleItem:true,
    transitionStyle:"backSlide",
    autoPlay:false,
    rewindNav : false,
    navigation:true,
    navigationText: [
        "<a id='testimonial-sidebar-prev' class='glyphicon glyphicon-chevron-left'></a>",
        "<a id='testimonial-sidebar-next' class='glyphicon glyphicon-chevron-right'></a>"
    ],

});

/*======================== Product Slider =============================*/

var owlSidebarProduct = $("#product-mobile-slider");
owlSidebarProduct.owlCarousel({
    items:1,
    slideSpeed : 3000,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    singleItem:true,
    transitionStyle:"backSlide",
    rewindNav : false,
    autoPlay:false,
    navigation:true,
    navigationText: [
        "<a id='feature-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
        "<a id='feature-page-next' class='glyphicon glyphicon-chevron-right'></a>"
    ],
});

