function productSliderOwl(items) {

    var owlCategory = $(".category-slider");
    owlCategory.owlCarousel({
        items: items ,
        slideSpeed : 500,
        itemsCustom : false,
        itemsDesktop : [1199,4],
        itemsDesktopSmall : [980,3],
        itemsTablet: [768,2],
        itemsTabletSmall: false,
        itemsMobile : [479,2],
        itemsScaleUp : false,
        autoPlay:false,
        lazyLoad : true,
        navigation : true,
        navigationText: [
            "<a id='prevCatItem-1' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='nextCatItem-1' class='glyphicon glyphicon-chevron-right'></a>"
        ],
        rewindNav : false,
        pagination : false
    });

    var owlCategory3 = $(".category-slider-3");
    owlCategory3.owlCarousel({
        items: 3 ,
        slideSpeed : 500,
        itemsCustom : false,
        itemsDesktop : [1199,4],
        itemsDesktopSmall : [980,3],
        itemsTablet: [768,2],
        itemsTabletSmall: false,
        itemsMobile : [479,1],
        itemsScaleUp : false,
        autoPlay:false,
        lazyLoad : true,
        navigation : true,
        navigationText: [
            "<a id='prevCatItem-1' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='nextCatItem-1' class='glyphicon glyphicon-chevron-right'></a>"
        ],
        rewindNav : false,
        pagination : false,
    });

    var owlCategory4 = $(".category-slider-4");
    owlCategory4.owlCarousel({
        items: 4 ,
        slideSpeed : 500,
        itemsCustom : false,
        itemsDesktop : [1199,4],
        itemsDesktopSmall : [980,3],
        itemsTablet: [768,2],
        itemsTabletSmall: false,
        itemsMobile : [479,1],
        itemsScaleUp : false,
        autoPlay:false,
        lazyLoad : true,
        navigation : true,
        navigationText: [
            "<a id='prevCatItem-1' class='glyphicon glyphicon-chevron-left'></a>",
            "<a id='nextCatItem-1' class='glyphicon glyphicon-chevron-right'></a>"
        ],
        rewindNav : false,
        pagination : false,
    });



    /* ================ End Category Product ===================*/

    /* =============== Start Feature Product =====================*/

    var owlPromotion = $(".promotion-slider");
    owlPromotion.owlCarousel({
        items: items ,
        slideSpeed : 500,
        itemsCustom : false,
        itemsDesktop : [1199,4],
        itemsDesktopSmall : [980,3],
        itemsTablet: [768,2],
        itemsTabletSmall: false,
        itemsMobile : [479,1],
        itemsScaleUp : false,
        autoPlay:false,
        navigation : true,
        lazyLoad : true,
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


    /* ======================================== End Feature Product ========================================================*/

}

/*

var searchSubItemConfig = function (subdomain,product) {

}

var categoryProductRes = $('#categoryProductRight').outerHeight(true);
$('.categoryProductLeft').css({
    'height':(categoryProductRes-30)
});

$('.categoryProductLeft .img-card-large').css({
    'height':(categoryProductRes-73)
});

$('.categoryProductLeft .img-card-large img').css({
    'height':(categoryProductRes-73)
});


var subCategoryProductRes = $('#subCategoryProductRight').outerHeight(true);
$('.subCategoryProductLeft').css({
    'height':(subCategoryProductRes-30)
});
$('.subCategoryProductLeft .img-card-cat').css({
    'height':(subCategoryProductRes-73)
});

$('.subCategoryProductLeft .img-card-cat img').css({
    'height':(subCategoryProductRes-73)
});


var categorySubCategoryRes = $('#categorySubCategoryRight').outerHeight(true);
$('#categorySubCategoryLeft').css({
    'height':(categorySubCategoryRes-30)
});

var brandHeight = $('#brandProductRight').outerHeight(true);
$('.brandProductLeft').css({
    'height':(brandHeight-30)
});

$('.brandProductLeft .img-card-large').css({
    'height':(brandHeight-73)
});

$('.brandProductLeft .img-card-large img').css({
    'height':(brandHeight-73)
});


var promotionHeight = $('#promotionProductRight').outerHeight(true);
$('.promotionProductLeft').css({
    'height':(promotionHeight-30)
});

$('.promotionProductLeft .img-card-large').css({
    'height':(promotionHeight-73)
});

$('.promotionProductLeft .img-card-large img').css({
    'height':(promotionHeight-73)
});

var tagHeight = $('#tagProductRight').outerHeight(true);
$('.tagProductLeft').css({
    'height':(tagHeight-30)
});

$('.tagProductLeft .img-card-large').css({
    'height':(tagHeight-73)
});

$('.tagProductLeft .img-card-large img').css({
    'height':(tagHeight-73)
});

var discountHeight = $('#discountProductRight').outerHeight(true);

$('.discountProductLeft').css({
    'height':(discountHeight-30)
});
*/

/*
$('.discountProductLeft .img-card-large').css({
    'height':(discountHeight-73)
});

$('.discountProductLeft .img-card-large img').css({
    'height':(discountHeight-73)
});
*/


