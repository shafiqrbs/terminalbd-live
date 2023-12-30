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
            if(obj['process'] == 'invalid'){
                alert('There is not enough product in stock at this moment');
            }else{
                $('.totalItem').html(obj['totalItem']);
                $('.totalAmount').html(obj['cartTotal']);
                $('.vsidebar .txt').html(obj['cartResult']);
            }
        }
    });
    e.preventDefault();

});


$('#cartItem').mouseover(function(){
    $('#cartItem').popModal({
        html : function(callback) {
            $.ajax({url:'/cart/product-details'}).done(function(content){
                callback(content);
            });
        }
    });
});

$(document).on( "click", ".cartSubmit", function(e){

    var url = $('.cartSubmit').attr("data-url");
    var data = $('.addCart').serialize();
    var qnt = $('#quantity').val();

    $.ajax({
        url:url ,
        type: 'POST',
        data:data,
        beforeSend: function () {
            $('.loader-double').fadeIn(5000).addClass('is-active');
            $('.cartSubmit').attr("disabled", true).html('<i class="fa fa-shopping-cart"></i> '+qnt+' in Basket');
        },
        success: function(response){
            $('.loader-double').fadeOut(5000).removeClass('is-active');
            obj = JSON.parse(response);
            $('.totalItem').html(obj['totalItem']);
            $('.totalAmount').html(obj['cartTotal']);
            $('.dropdown-cart').html(obj['salesItem']);
            $('.vsidebar .txt').html(obj['cartResult']);

        }
    });
    e.preventDefault();

});


$(document).on( "click", ".hunger-remove-cart", function(e){
    var url = $(this).attr("data-url");
    var id = $(this).attr("id");
    $('#item-remove-'+id).hide();
    $.ajax({
        url:url ,
        type: 'GET',
        success: function(response){
            obj = JSON.parse(response);
            $('#cart-item-list-box').html(obj['cartItem']);
            $('.totalItem').html(obj['totalItem']);
            $('.totalAmount').html(obj['cartTotal']);
            $('.vsidebar .txt').html(obj['cartResult']);

        }
    });
    e.preventDefault();
});



$('.remove-cart').click( function(e) {

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

$('.product-preview').click(function () {

    var url = $(this).attr("data-url");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            $('.product-modal-content').html(response);
            $('#product-modal').modal('toggle');
        }
    })
});

$(document).on( "click", ".preview", function(e){
    var url = $(this).attr("data-url");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            $('.product-modal-content').html(response);
            $('#product-modal').modal('toggle');
        }
    })
});

$(document).on( "click", ".btn-number-cart", function(e){

    e.preventDefault();

    var url         = $(this).attr('data-url');
    var productId   = $(this).attr('data-text');
    var price       = $(this).attr('data-title');
    var fieldId     = $(this).attr('data-id');
    var fieldName   = $(this).attr('data-field');
    var type        = $(this).attr('data-type');
    var input       = $('#quantity-'+ $(this).attr('data-id'));
    var currentVal  = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            if(currentVal > input.attr('min')) {
                var existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                    .done(function( data ) {
                        obj = JSON.parse(data);
                        var subTotal = (existVal * parseInt(price));
                        $('#btn-total-'+fieldId).html(subTotal);
                        $('#cart-item-list-box').html(obj['cartItem']);
                        $('.totalItem').html(obj['totalItem']);
                        $('.totalAmount').html(obj['cartTotal']);
                        $('.vsidebar .txt').html(obj['cartResult']);
                    });
            }
            if(parseInt(input.val()) == input.attr('min')) {
                $('#'+input).attr('disabled', true);
            }else {
                $('#'+input).attr('disabled', false);
            }

        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                var existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                    .done(function(data){
                        obj = JSON.parse(data);
                        if(obj['process'] == 'success'){
                            var subTotal = (existVal * parseInt(price));
                            $('#btn-total-'+fieldId).html(subTotal);
                            $('#cart-item-list-box').html(obj['cartItem']);
                            $('.totalItem').html(obj['totalItem']);
                            $('.totalAmount').html(obj['cartTotal']);
                            $('.vsidebar .txt').html(obj['cartResult']);
                        }else{
                            input.val(existVal-1).change();
                            alert('There is not enough product in stock at this moment')
                        }
                    });
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $('#'+input).attr('disabled', true);
            }else {
                $('#'+input).attr('disabled', false);
            }

        }
    } else {
        input.val(0);
    }
});

$(document).on( "click", ".btn-inline-cart", function(e){

    e.preventDefault();

    var url         = $(this).attr('data-url');
    var productId   = $(this).attr('data-text');
    var price       = $(this).attr('data-title');
    var fieldId     = $(this).attr('data-id');
    var fieldName   = $(this).attr('data-field');
    var type        = $(this).attr('data-type');
    var input       = $('#quantity-'+ $(this).attr('data-id'));
    var currentVal  = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            if(currentVal > input.attr('min')) {
                var existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                    .done(function( data ) {
                        obj = JSON.parse(data);
                        var subTotal = (existVal * parseInt(price));
                        $('#btn-total-'+fieldId).html(subTotal);
                        $('#cart-item-list-box').html(obj['cartItem']);
                        $('.totalItem').html(obj['totalItem']);
                        $('.totalAmount').html(obj['cartTotal']);
                        $('.vsidebar .txt').html(obj['cartResult']);
                    });
            }
            if(parseInt(input.val()) == input.attr('min')) {
                $('#'+input).attr('disabled', true);
            }else {
                $('#'+input).attr('disabled', false);
            }

        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                var existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                    .done(function(data){
                        obj = JSON.parse(data);
                        if(obj['process'] == 'success'){
                            var subTotal = (existVal * parseInt(price));
                            $('#btn-total-'+fieldId).html(subTotal);
                            $('#cart-item-list-box').html(obj['cartItem']);
                            $('.totalItem').html(obj['totalItem']);
                            $('.totalAmount').html(obj['cartTotal']);
                            $('.vsidebar .txt').html(obj['cartResult']);
                        }else{
                            input.val(existVal-1).change();
                            alert('There is not enough product in stock at this moment')
                        }
                    });
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $('#'+input).attr('disabled', true);
            }else {
                $('#'+input).attr('disabled', false);
            }

        }
    } else {
        input.val(0);
    }
});

$(document).on( "click", "#productBuy", function(e){


    var url = $('#productBuy').attr("data-url");
    var data = $('.addCart').serialize();
    var qnt = $('#quantity').val();

    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('.loader-curtain').fadeIn(5000).addClass('is-active');
            $.post(url,data).done(function(response) {
                obj = JSON.parse(response);
                $('#productBuy').addClass('shopping-cart').attr("disabled", true).html('<i class="fa fa-shopping-cart"></i> '+qnt+' in Basket');
                $('.totalItem').html(obj['totalItem']);
                $('.totalAmount').html(obj['cartTotal']);
                $('.dropdown-cart').html(obj['salesItem']);
                $('.vsidebar .txt').html(obj['cartResult']);
                $('.loader-curtain').fadeOut(1000);
            }).always(function() {
                $('#product-confirm').notifyModal({
                    duration : 10000,
                    placement : 'center',
                    overlay : true,
                    type : 'notify',
                    icon : false
                });
            });
        }
    });
});

$(document).on( "click", ".product-buy", function(e){

    var cartForm = $(this).closest("form");
    var url = $(this).attr("data-url");
    var dataId = $(this).attr("data-id");
    var size = $('#size-'+dataId , cartForm).val() != '' ? $('#size-'+dataId).val() : '';
    var color = $('#color-'+dataId, cartForm).val() != '' ? $('#color-'+dataId).val() : '';
    var productImg = $('#productImg-'+dataId, cartForm).val() != '' ? $('#productImg-'+dataId).val() : '';

    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('.loader-curtain').fadeIn(5000).addClass('is-active');
            $.get(url,{ size:size, color: color,productImg:productImg } ).done(function(response) {
                obj = JSON.parse(response);
                $('#buy-'+dataId).addClass('shopping-cart').attr("disabled", true).html('<i class="fa fa-shopping-cart"></i> 1 in Basket');
                $('.totalItem').html(obj['totalItem']);
                $('.totalAmount').html(obj['cartTotal']);
                $('.dropdown-cart').html(obj['salesItem']);
                $('.vsidebar .txt').html(obj['cartResult']);
                $('.loader-curtain').fadeOut(1000);
            }).always(function() {
                $('#product-confirm').notifyModal({
                    duration : 10000,
                    placement : 'center',
                    overlay : true,
                    type : 'notify',
                    icon : false
                });
            });
        }
    });
});


$(document).on( "change", ".inlineSizeChange", function( e ) {

    var url = $(this).attr("data-url");
    var subItem = $(this).val();
    var product = $(this).attr("data-id");
    $.get(url,{'subItem':subItem}).done(function(response) {
        obj = JSON.parse(response);
        $('#inlineLoading-'+product).html(obj['subItem']);
        $('#currency-'+product).html(obj['salesPrice']);
    });
});

$(document).on( "click", "#spec", function(e){
    $('#showSpec').slideToggle('2000');
    $("span", this).toggleClass("glyphicon-chevron-down glyphicon-chevron-up");
});


$(document).on( "click", ".btn-number", function(e){

    e.preventDefault();

    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $("input[name='"+fieldName+"']");
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {

            if(currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            }
            if(parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
            }
        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
            }
        }
    } else {
        input.val(0);
    }
});


$("div.list-group > a").click(function(e) {
    e.preventDefault();
    $(this).siblings('a.active').removeClass("active");
    $(this).addClass("active");
    var index = $(this).index();
    $("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
    $("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
});


/*


$('.input-number').focusin(function(){
    $(this).data('oldValue', $(this).val());
});
$('.input-number').change(function() {

    minValue =  parseInt($(this).attr('min'));
    maxValue =  parseInt($(this).attr('max'));
    valueCurrent = parseInt($(this).val());

    name = $(this).attr('name');
    if(valueCurrent >= minValue) {
        $(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('Sorry, the minimum value was reached');
        $(this).val($(this).data('oldValue'));
    }
    if(valueCurrent <= maxValue) {
        $(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('Sorry, the maximum value was reached');
        $(this).val($(this).data('oldValue'));
    }


});
$(".input-number").keydown(function (e) {

    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
        // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) ||
        // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39)) {
        // let it happen, don't do anything
        return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});
*/
