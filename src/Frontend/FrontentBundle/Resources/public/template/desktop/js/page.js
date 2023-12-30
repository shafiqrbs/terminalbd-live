function sliderPageOwl(columnItem) {

    var owlFeaturePageSlider = $("#feature-page-slider");
    owlFeaturePageSlider.owlCarousel({
        items:columnItem,
        slideSpeed : 3000,
        itemsDesktop:[1199,3],
        itemsDesktopSmall:[979,2],
        itemsTablet:[768,1],
        pagination: false,
        paginationNumbers: false,
        autoPlay:false,
        rewindNav:false,
        navigation:true,
        navigationText: [
            "<a id='feature-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='feature-page-next' class='glyphicon glyphicon-chevron-right'></a>"
        ],
    });


    /*======================== End Page Slider =============================*/


    /*======================== Blog Slider =============================*/

    var owlHeaderFooterBlog = $("#blog-page-slider");
    owlHeaderFooterBlog.owlCarousel({
        items:columnItem,
        slideSpeed : 3000,
        itemsDesktop:[1199,3],
        itemsDesktopSmall:[979,2],
        itemsTablet:[768,1],
        pagination: false,
        paginationNumbers: false,
        autoPlay:false,
        rewindNav:false,
        navigation:true,
        navigationText: [
            "<a id='blog-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='blog-page-next' class='glyphicon glyphicon-chevron-right'></a>"
        ],
    });


    /*======================== End Blog Slider =============================*/

    /*======================== Branch Slider =============================*/

    var owlHeaderFooterBranch = $("#branch-page-slider");
    owlHeaderFooterBranch.owlCarousel({
        items:columnItem,
        slideSpeed : 3000,
        itemsDesktop:[1199,3],
        itemsDesktopSmall:[979,2],
        itemsTablet:[768,1],
        pagination: false,
        paginationNumbers: false,
        autoPlay:false,
        rewindNav:false,
        navigation:true,
        navigationText: [
            "<a id='branch-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='branch-page-next' class='glyphicon glyphicon-chevron-right'></a>"
        ],
    });


    /*======================== End Branch Slider =============================*/

    /*======================== Client Slider =============================*/

    var owlHeaderFooterClient = $("#client-page-slider");
    owlHeaderFooterClient.owlCarousel({
        items:6,
        slideSpeed : 3000,
        itemsDesktop:[1199,2],
        itemsDesktopSmall:[979,2],
        itemsTablet:[768,2],
        pagination: false,
        paginationNumbers: false,
        autoPlay:false,
        rewindNav:false,
        navigation:true,
        navigationText: [
            "<a id='client-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='client-page-next' class='glyphicon glyphicon-chevron-right'></a>"
        ],
    });



    /*======================== End Client Slider =============================*/


    /*======================== Event Slider =============================*/

    var owlHeaderFooterEvent = $("#event-page-slider");
    owlHeaderFooterEvent.owlCarousel({
        items:columnItem,
        slideSpeed : 3000,
        itemsDesktop:[1199,3],
        itemsDesktopSmall:[979,2],
        itemsTablet:[768,1],
        pagination: false,
        paginationNumbers: false,
        autoPlay:false,
        rewindNav:false,
        navigation:true,
        navigationText: [
            "<a id='event-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='event-page-next' class='glyphicon glyphicon-chevron-right'></a>"
        ],
    });


    /*======================== News Slider =============================*/

    var owlHeaderFooterNews = $("#news-page-slider");
    owlHeaderFooterNews.owlCarousel({
        items:columnItem,
        slideSpeed : 3000,
        itemsDesktop:[1199,3],
        itemsDesktopSmall:[979,2],
        itemsTablet:[768,1],
        pagination: false,
        paginationNumbers: false,
        autoPlay:false,
        rewindNav:false,
        navigation:true,
        navigationText: [
            "<a id='news-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='news-page-next' class='glyphicon glyphicon-chevron-right'></a>"
        ],
    });

    /*======================== Notice Slider =============================*/

    var owlHeaderFooterNotice = $("#notice-page-slider");
    owlHeaderFooterNotice.owlCarousel({
        items:columnItem,
        slideSpeed : 3000,
        itemsDesktop:[1199,3],
        itemsDesktopSmall:[979,2],
        itemsTablet:[768,1],
        pagination: false,
        paginationNumbers: false,
        autoPlay:false,
        rewindNav:false,
        navigation:true,
        navigationText: [
            "<a id='notice-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='notice-page-next' class='glyphicon glyphicon-chevron-right'></a>"
        ],
    });


    /*======================== Portfolio Slider =============================*/

    var owlHeaderFooterPortfolio = $("#portfolio-page-slider");
    owlHeaderFooterPortfolio.owlCarousel({
        items:columnItem,
        slideSpeed : 3000,
        itemsDesktop:[1199,3],
        itemsDesktopSmall:[979,2],
        itemsTablet:[768,1],
        pagination: false,
        paginationNumbers: false,
        autoPlay:false,
        rewindNav:false,
        navigation:true,
        navigationText: [
            "<a id='portfolio-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='portfolio-page-next' class='glyphicon glyphicon-chevron-right'></a>"
        ],

    });


    /*======================== Service Slider =============================*/

    var owlHeaderFooterService = $("#service-page-slider");
    owlHeaderFooterService.owlCarousel({
        items:columnItem,
        slideSpeed : 3000,
        itemsDesktop:[1199,3],
        itemsDesktopSmall:[979,2],
        itemsTablet:[768,1],
        pagination: false,
        paginationNumbers: false,
        autoPlay:false,
        rewindNav:false,
        navigation:true,
        navigationText: [
            "<a id='service-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='service-page-next' class='glyphicon glyphicon-chevron-right'></a>"
        ],

    });


    /*======================== Sponsor Slider =============================*/

    var owlHeaderFooterSponsor = $("#sponsor-page-slider");
    owlHeaderFooterSponsor.owlCarousel({
        items:6,
        slideSpeed : 3000,
        itemsDesktop:[1199,3],
        itemsDesktopSmall:[979,2],
        itemsTablet:[768,1],
        pagination: false,
        paginationNumbers: false,
        autoPlay:false,
        rewindNav:false,
        navigation:true,
        navigationText: [
            "<a id='sponsor-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='sponsor-page-next' class='glyphicon glyphicon-chevron-right'></a>"
        ],

    });

    /*======================== Team Slider =============================*/

    var owlHeaderFooterTeam = $("#team-page-slider");
    owlHeaderFooterTeam.owlCarousel({
        items:columnItem,
        slideSpeed : 3000,
        itemsDesktop:[1199,3],
        itemsDesktopSmall:[979,2],
        itemsTablet:[768,1],
        pagination: false,
        paginationNumbers: false,
        autoPlay:false,
        rewindNav:false,
        navigation:true,
        navigationText: [
            "<a id='team-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='team-page-next' class='glyphicon glyphicon-chevron-right'></a>"
        ],


    });

    /*======================== Testimonial Slider =============================*/

    var owlHeaderFooterTestimonial = $("#testimonial-page-slider");

    owlHeaderFooterTestimonial.owlCarousel({
        items:1,
        slideSpeed : 3000,
        itemsDesktop:[1000,2],
        itemsDesktopSmall:[979,1],
        itemsTablet:[768,1],
        pagination:false,
        singleItem:true,
        transitionStyle:"backSlide",
        autoPlay:false,
        rewindNav : false,
        navigation:true,
        navigationText: [
            "<a id='testimonial-page-prev' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='testimonial-page-next' class='glyphicon glyphicon-chevron-right'></a>"
        ],

    });

}

