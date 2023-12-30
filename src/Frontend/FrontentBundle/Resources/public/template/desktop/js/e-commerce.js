function productSliderOwl(items) {

    var owlCategory = $(".category-slider");
    owlCategory.owlCarousel({
        items: items ,
        slideSpeed : 1000,
        itemsCustom : false,
        itemsDesktop : [1199,4],
        itemsDesktopSmall : [980,3],
        itemsTablet: [768,2],
        itemsTabletSmall: false,
        itemsMobile : [479,1],
        itemsScaleUp : false,
        autoPlay:false,
        navigation : true,
        navigationText: [
            "<a id='prevCatItem-1' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='nextCatItem-1' class='glyphicon glyphicon-chevron-right'></a>"
        ],
        rewindNav : false,
        pagination : false,
    });

    var owlCategory3 = $(".category-slider-3");
    owlCategory3.owlCarousel({
        items: 3 ,
        slideSpeed : 1000,
        itemsCustom : false,
        itemsDesktop : [1199,4],
        itemsDesktopSmall : [980,3],
        itemsTablet: [768,2],
        itemsTabletSmall: false,
        itemsMobile : [479,1],
        itemsScaleUp : false,
        autoPlay:false,
        navigation : true,
        navigationText: [
            "<a id='prevCatItem-1' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='nextCatItem-1' class='glyphicon glyphicon-chevron-right'></a>"
        ],
        rewindNav : false,
        pagination : false,
    });


    /* ======================================== End Category Product ========================================================*/

    /* ======================================== Start Feature Product ========================================================*/

    var owlPromotion = $(".promotion-slider");
    owlPromotion.owlCarousel({
        items: items ,
        slideSpeed : 5000,
        itemsCustom : false,
        itemsDesktop : [1199,4],
        itemsDesktopSmall : [980,3],
        itemsTablet: [768,2],
        itemsTabletSmall: false,
        itemsMobile : [479,1],
        itemsScaleUp : false,
        autoPlay:false,
        navigation : true,
        navigationText: [
            "<a id='prevPromotion-1' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='nextPromotion-1' class='glyphicon glyphicon-chevron-right'></a>"
        ],
        rewindNav : false,
        pagination : false,
    });

    /* ======================================== End Feature Product ========================================================*/

    /* ======================================== Start Feature Product ========================================================*/

    var owlTag = $(".tag-slider");
    owlTag.owlCarousel({
        items: items ,
        slideSpeed : 5000,
        itemsCustom : false,
        itemsDesktop : [1199,4],
        itemsDesktopSmall : [980,3],
        itemsTablet: [768,2],
        itemsTabletSmall: false,
        itemsMobile : [479,1],
        itemsScaleUp : false,
        autoPlay:false,
        navigation : true,
        navigationText: [
            "<a id='prevTag-1' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='nextTag-1' class='glyphicon glyphicon-chevron-right'></a>"
        ],
        rewindNav : false,
        pagination : false,
    });


    /* ======================================== End Feature Product ========================================================*/

    /* ======================================== Start Discount Product ========================================================*/

    var owlDiscount = $(".discount-slider");
    owlDiscount.owlCarousel({
        items: items ,
        slideSpeed : 5000,
        itemsCustom : false,
        itemsDesktop : [1199,4],
        itemsDesktopSmall : [980,3],
        itemsTablet: [768,2],
        itemsTabletSmall: false,
        itemsMobile : [479,1],
        itemsScaleUp : false,
        autoPlay:false,
        navigation : true,
        navigationText: [
            "<a id='prevDiscount-1' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='nextDiscount-1' class='glyphicon glyphicon-chevron-right'></a>"
        ],
        rewindNav : false,
        pagination : false,
    });

    /* ============================== End Feature Product =============================*/

    /* ================================ Start Feature Product ==========================*/

    var owlBrand = $(".brand-slider");
    owlBrand.owlCarousel({
        items: items ,
        slideSpeed : 5000,
        itemsCustom : false,
        itemsDesktop : [1199,4],
        itemsDesktopSmall : [980,3],
        itemsTablet: [768,2],
        itemsTabletSmall: false,
        itemsMobile : [479,1],
        itemsScaleUp : false,
        autoPlay:false,
        navigation : true,
        navigationText: [
            "<a id='prevBrand-1' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='nextBrand-1' class='glyphicon glyphicon-chevron-right'></a>"
        ],
        rewindNav : false,
        pagination : false,
    });

    /* ======================================== End Feature Product =====================================*/


    /* ================================== Start Feature Category ==============================*/

    var owlFeatureCategory = $(".feature-category-slider");
    owlFeatureCategory.owlCarousel({
        items: items ,
        slideSpeed : 5000,
        itemsCustom : false,
        itemsDesktop : [1199,4],
        itemsDesktopSmall : [980,3],
        itemsTablet: [768,2],
        itemsTabletSmall: false,
        itemsMobile : [479,1],
        itemsScaleUp : false,
        autoPlay:false,
        navigation : true,
        navigationText: [
            "<a id='featureCategorySlider' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='featureCategorySlider' class='glyphicon glyphicon-chevron-right'></a>"
        ],
        rewindNav : false,
        pagination : false,
    });


    /* ======================================== End Feature Product ========================================================*/

}
