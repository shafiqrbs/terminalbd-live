function productSliderOwl(items = 1) {

    $(".mobile-category-slider").owlCarousel({
        items: items,
        slideSpeed: 500,
        itemsCustom: false,
        itemsDesktop: [1199, 4],
        itemsDesktopSmall: [980, 3],
        itemsTablet: [768, 2],
        itemsTabletSmall: false,
        itemsMobile: [479, items],
        itemsScaleUp: false,
        autoPlay: false,
        lazyLoad: true,
        navigation: true,
        navigationText: [
            "<a id='prevCatItem-1' class='fa fa-arrow-left'></a>",
            "<a id='nextCatItem-1' class='fa fa-arrow-right'></a>"
        ],
        rewindNav: false,
        pagination: false,
    });
}


