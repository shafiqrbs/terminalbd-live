$( ".category-list" ).click(function() {
    $('.home-category').slideToggle(500);
});

$(document).on( "change", ".modalChangeSubItem", function( e ) {

    var url = $(this).attr("data-url");
    $.get(url).done(function(response) {
        obj = JSON.parse(response);
        $('#sub-product-load').html(obj['colors']);
        $('#modal-sales-price').val(obj['salesPrice']);
        $('#modal-item-unit').html(obj['unit']);
        $('#itemPrice').html(obj['itemPrice']);
        $('#unit').html(obj['unit']);
    });
});

$(document).on( "change", ".modalMobileChangeSubItem", function( e ) {

    var url = $(this).attr("data-url");
    var size = $(this).attr("data-id");
    $.get(url).done(function(response) {
        obj = JSON.parse(response);
        $('#size').val(size);
        $('#sub-product-load').html(obj['colors']);
        $('#modal-item-unit').html(obj['unit']);
        $('#itemPrice').html(obj['itemPrice']);
        $('#unit').html(obj['unit']);
        $('.cartSubmit').attr("disabled", false);
    });

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
        success: function(response) {
            $('#subItemDetails').html(response);
        },

    })

});


/*$('.addCart').submit( function(e) {

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

});*/


function cartInfo(response,quantity) {
    obj = JSON.parse(response);
    var qnt = quantity === "" ? 1 : quantity;
    $('.cartSubmit').attr("disabled", true).html(qnt+' in Basket');
    setTimeout(function(){$('.cartSubmit').html('<i class="fa fa-shopping-cart"></i> ADD')}, 3000);
    $('.totalItem').html(obj['totalItems']);
    $('.totalAmount').html(obj['cartTotal']);
    $('.vsidebar .txt').html(obj['cartResult']).show().fadeOut(3000);
}

$(document).on( "click", ".cartSubmit", function(e){

    var url = $(this).attr("data-url");
    var quantity = $('#quantity').val() !== '' ? $('#quantity').val() : '';
    var subItem = $('#size').val() !== '' ? $('#size').val() : '';
    var color = $('#color').val() !== '' ? $('#color').val() : '';
    var productImg = $('#productImg').val() !== '' ? $('#productImg').val() : '';
    $.get(url,{ quantity:quantity,subItem:subItem, color: color,productImg:productImg})
        .done(function(response) {
            cartInfo(response,quantity);
        });
    e.preventDefault();
});

$(document).on( "click", ".product-inline-buy", function(e){

    var dataId = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    var quantity = $('#quant-'+dataId).val() !== '' ? $('#quant-'+dataId).val() : '';
    var subItem = $('#size-'+dataId).val() !== '' ? $('#size-'+dataId).val() : '';
    var color = $('#color-'+dataId).val() !== '' ? $('#color-'+dataId).val() : '';
    var productImg = $('#productImg-'+dataId).val() !== '' ? $('#productImg-'+dataId).val() : '';
    $.get(url,{ quantity:quantity,subItem:subItem, color: color,productImg:productImg})
        .done(function(response) {
            $('#productBuy-'+dataId).attr("disabled", true).html(quantity+' in Basket');
            setTimeout(function(){$('#productBuy-'+dataId).attr("disabled", false).html('<i class="fa fa-shopping-cart"></i> ADD')}, 3000);
            cartInfo(response,quantity);
        });
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

$('.product-preview , .item-preview').click(function () {

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

/*$(document).on( "click", ".btn-new-cart-item", function(e){

    e.preventDefault();
    productId      = $(this).attr('data-id');
    fieldName   = $(this).attr('data-field');
    type        = $(this).attr('data-type');
    input = $("input[name='"+fieldName+"']");
    currentVal = parseInt(input.val()) ? parseInt(input.val()) : 0;
    if (!isNaN(currentVal)) {
        if(type === 'minus') {
            if(currentVal > input.attr('min')) {
                existVal = (currentVal - 1);
                input.val(existVal).change();
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $(input).attr('disabled', true);
            }else {
                $(input).attr('disabled', false);

            }

        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                existVal = (currentVal + 1);
                input.val(existVal).change();
            }
            if(parseInt(input.val()) === input.attr('max')) {
                $(input).attr('disabled', true);
            }else {
                $(input).attr('disabled', false);
            }

        }
    } else {
        input.val(0);
    }
});*/

$(document).on( "change", ".inlineSizeChange", function( e ) {

    var url = $(this).attr("data-url");
    var subItem = $(this).val();
    var product = $(this).attr("data-id");
    $.ajax({
        url: url,
        crossOrigin: true,
        data: 'subItem='+subItem,
        type: 'GET',
        success: function(response) {
            obj = JSON.parse(response);
            $('#inlineLoading-'+product).html(obj['subItem']);
            $('#currency-'+product).html(obj['salesPrice']);
            $('#subSize-'+product).html(obj['size']);
            $('#subSizeUnit-'+product).html(obj['sizeUnit']);
        }
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

