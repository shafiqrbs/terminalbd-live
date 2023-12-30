/*======================== Page Slider ============================*/

var owlFeatureSidebarSLider = $("#feature-sidebar-slider");
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

var owlSidebarBlog = $("#blog-sidebar-slider");
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

var owlSidebarBranch = $("#branch-sidebar-slider");
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

var owlSidebarClient = $("#client-sidebar-slider");
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

var owlSidebarEvent = $("#event-sidebar-slider");
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

var owlSidebarNews = $("#news-sidebar-slider");
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

var owlSidebarNotice = $("#notice-sidebar-slider");
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

var owlSidebarPortfolio = $("#portfolio-sidebar-slider");
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

var owlSidebarService = $("#service-sidebar-slider");
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

var owlSidebarSponsor = $("#sponsor-sidebar-slider");
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

var owlSidebarTeam = $("#team-sidebar-slider");
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

var owlSidebarTestimonial = $("#testimonial-sidebar-slider");
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


/*!
 *  jQuery sidebar plugin
 *  ---------------------
 *  A stupid simple sidebar jQuery plugin.
 *
 *  Developed with <3 and JavaScript by the jillix developers.
 *  Copyright (c) 2013-15 jillix
 * */
(function($) {

    /**
     * sidebar
     * Initialize sidebar on selected elements.
     *
     * ```js
     * $(".my-sidebar").sidebar({...});
     * ```
     *
     * After the call above, you can programatically open/close/toggle the sidebar using:
     *
     * ```js
     * $(".my-sidebar").trigger("sidebar:open");
     * $(".my-sidebar").trigger("sidebar:close");
     * $(".my-sidebar").trigger("sidebar:toggle");
     * $(".my-sidebar").trigger("sidebar:close", [{ speed: 0 }]);
     * ```
     *
     * After the sidebar is opened/closed, `sidebar:opened`/`sidebar:closed` event is emitted.
     *
     * ```js
     * $(".my-sidebar").on("sidebar:opened", function () {
     *    // Do something on open
     * });
     *
     * $(".my-sidebar").on("sidebar:closed", function () {
     *    // Do something on close
     * });
     * ```
     *
     * @name sidebar
     * @function
     * @param {Object} options An object that will be merged with the default options.
     *
     *  - `speed` (Number): animation speed (default: `200`)
     *  - `side` (String): left|right|top|bottom (default: `"left"`)
     *  - `isClosed` (Boolean): A boolean value indicating if the sidebar is closed or not (default: `false`).
     *  - `close` (Boolean): If `true`, the sidebar will be closed by default.
     *
     * @return {jQuery} The jQuery elements that were selected.
     */
    $.fn.sidebar = function(options) {

        var self = this;
        if (self.length > 1) {
            return self.each(function () {
                $(this).sidebar(options);
            });
        }

        // Width, height
        var width = self.outerWidth();
        var height = self.outerHeight();

        // Defaults
        var settings = $.extend({

            // Animation speed
            speed: 200,

            // Side: left|right|top|bottom
            side: "left",

            // Is closed
            isClosed: false,

            // Should I close the sidebar?
            close: true

        }, options);

        /*!
         *  Opens the sidebar
         *  $([jQuery selector]).trigger("sidebar:open");
         * */
        self.on("sidebar:open", function(ev, data) {
            var properties = {};
            properties[settings.side] = 0;
            settings.isClosed = null;
            self.stop().animate(properties, $.extend({}, settings, data).speed, function() {
                settings.isClosed = false;
                self.trigger("sidebar:opened");
            });
        });


        /*!
         *  Closes the sidebar
         *  $("[jQuery selector]).trigger("sidebar:close");
         * */
        self.on("sidebar:close", function(ev, data) {
            var properties = {};
            if (settings.side === "left" || settings.side === "right") {
                properties[settings.side] = -self.outerWidth();
            } else {
                properties[settings.side] = -self.outerHeight();
            }
            settings.isClosed = null;
            self.stop().animate(properties, $.extend({}, settings, data).speed, function() {
                settings.isClosed = true;
                self.trigger("sidebar:closed");
            });
        });

        /*!
         *  Toggles the sidebar
         *  $("[jQuery selector]).trigger("sidebar:toggle");
         * */
        self.on("sidebar:toggle", function(ev, data) {
            if (settings.isClosed) {
                self.trigger("sidebar:open", [data]);
            } else {
                self.trigger("sidebar:close", [data]);
            }
        });

        function closeWithNoAnimation() {
            self.trigger("sidebar:close", [{
                speed: 0
            }]);
        }

        // Close the sidebar
        if (!settings.isClosed && settings.close) {
            closeWithNoAnimation();
        }

        $(window).on("resize", function () {
            if (!settings.isClosed) { return; }
            closeWithNoAnimation();
        });

        self.data("sidebar", settings);

        return self;
    };

    // Version
    $.fn.sidebar.version = "3.3.2";
})(jQuery);

