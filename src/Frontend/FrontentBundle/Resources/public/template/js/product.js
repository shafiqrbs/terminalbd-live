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

$('.discountProductLeft .img-card-large').css({
    'height':(discountHeight-73)
});

$('.discountProductLeft .img-card-large img').css({
    'height':(discountHeight-73)
});


$(document).on( "click", "#filter", function(e){
    alert('Submit');
    $('#productFilter').submit();

});

$(document).on( "change", ".modalChange", function( e ) {

    var subItem = $(this).val();
    var url = $(this).attr("data-url");
    $.ajax({
        url: url ,
        type: 'GET',
        data:'subItem='+subItem,
        success: function(response) {
            $('.modal-content').html(response);
        },

    })

});

$(document).on( "change", ".changeSize", function( e ) {

    var subItem = $(this).val();
    var url = $(this).attr("data-url");
    $.ajax({
        url: url ,
        type: 'GET',
        data:'subItem='+subItem,
        beforeSend: function() {
            $('#subItemDetails').show().addClass('loading').fadeIn(3000);
        },
        success: function(response) {
            $('#subItemDetails').html(response);
            $('#subItemDetails').removeClass('loading');
        },

    })

});

$(document).on( "change", ".changeCartSize", function( e ) {

    var subItem = $(this).val();
    var url = $(this).attr("data-url");
    $.ajax({
        url: url ,
        type: 'GET',
        data:'subItem='+subItem,
        beforeSend: function() {
            $('#subItemDetails').show().addClass('loading').fadeIn(3000);
        },
        success: function(response) {
            $('#subItemDetails').html(response);
            $('#subItemDetails').removeClass('loading');
        },

    })

});

$('.addCart').submit( function(e) {

    var url = $('.cartSubmit').attr("data-url");
    $.ajax({
        url:url ,
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(response){
            obj = JSON.parse(response);
            $('.totalItem').html(obj['totalItem']);
            $('.totalAmount').html(obj['cartTotal']);
            $('.item-list').html(obj['salesItem']);
            $('.vsidebar .txt').html(obj['cartResult']);
        }
    });
    e.preventDefault();

});

$('.update-cart').click( function() {

    var url = $(this).attr("data-url");
    var price = $(this).attr("data-id");
    var rowid = $(this).attr("id");
    var quantity = $('#'+rowid).val();
    $.ajax({
        url:url ,
        type: 'GET',
        data:'price='+price+'&quantity='+quantity,
        success: function(response){
            location.reload();
        }
    });
    e.preventDefault();

});


$(document).on( "click", ".cartSubmit", function(e){

    var url = $('.cartSubmit').attr("data-url");
    var data = $('.addCart').serialize();
    $.ajax({
        url:url ,
        type: 'POST',
        data:data,
        success: function(response){
            obj = JSON.parse(response);
            $('.totalItem').html(obj['totalItem']);
            $('.totalAmount').html(obj['cartTotal']);
            $('.dropdown-cart').html(obj['salesItem']);
            $('.vsidebar .txt').html(obj['cartResult']);

        }
    });
    e.preventDefault();

});




$('.remove-cart').click( function() {

    var url = $(this).attr("data-url");
    $.ajax({
        url:url ,
        type: 'GET',
        success: function(response){
            location.reload();
        }
    });
    e.preventDefault();

});

$('.preview').click(function () {

    var url = $(this).attr("data-url");
    $('.modal-body').html('');
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            $('.modal-content').html(response);
            $('#myModal').modal('toggle')
        }
    })
});

