function sliderTagCategoryOwl(columnItem) {
    
    var owlTagCategory = $("#tag-category-carousel");
    owlTagCategory.owlCarousel({

        items: columnItem,
        slideSpeed: 5000,
        itemsCustom: false,
        itemsDesktop: [1199, 4],
        itemsDesktopSmall: [980, 3],
        itemsTablet: [768, 2],
        itemsTabletSmall: false,
        itemsMobile: [479, 1],
        itemsScaleUp: false,
        autoPlay: false,
        navigation: true,
        navigationText: [
            "<a id='prevTagCategory' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='nextTagCategory' class='glyphicon glyphicon-chevron-right'></a>"
        ],
        rewindNav: false,
        pagination: false,

    });
}

