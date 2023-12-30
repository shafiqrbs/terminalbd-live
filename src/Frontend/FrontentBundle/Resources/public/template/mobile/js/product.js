$(document).on( "click", ".selector", function(e){
    var show = $(this).attr('data-id');
    $('#'+show).slideToggle();
    $("span", this).toggleClass("glyphicon-chevron-down glyphicon-chevron-up");
});

$('.carousel').carousel({
    interval: 4000
});

$('#OneColumnCarousel').carousel({
    interval: 40000
});

$('#TwoColumnCarousel').carousel({
    interval: 5000000
});

$('.twoColumnCarousel .item').each(function(){
    var next = $(this).next();
    if (!next.length) {
        next = $(this).siblings(':first');
    }
    next.children(':first-child').clone().appendTo($(this));
    if (next.length > 0) {
        next.addClass('rightest');
    }
    else {
        $(this).siblings(':first').children(':first-child').clone().appendTo($(this));
    }
});

$('#ThreeColumnCarousel').carousel({
    interval: 60000
});

$('.threeColumnCarousel .item').each(function(){
    var next = $(this).next();
    if (!next.length) {
        next = $(this).siblings(':first');
    }
    next.children(':first-child').clone().appendTo($(this));

    if (next.next().length > 0) {

        next.next().children(':first-child').clone().appendTo($(this)).addClass('rightest');

    }else {

        $(this).siblings(':first').children(':first-child').clone().appendTo($(this));

    }
});

$('#FourColumnCarousel').carousel({
    interval: false
});

$('.fourColumnCarousel .item').each(function(){
    var next = $(this).next();
    if (!next.length) {
        next = $(this).siblings(':first');
    }
    next.children(':first-child').clone().appendTo($(this));
    if (next.next().length > 0) {
        next.next().children(':first-child').clone().appendTo($(this));
        if (next.next().length > 0) {
            next.next().children(':first-child').clone().appendTo($(this)).addClass('rightest');
        }
    }else {

        $(this).siblings(':first').children(':first-child').clone().appendTo($(this));

    }
});


var itemArr = $.cookie('productList') ? $.cookie('productList'): 'faThLarge';
$("#"+itemArr).addClass('btn-active');

$('body').on('click', '.product-next-prev', function(e) {
    var url = $(this).attr('data-url');
    var dataTitle = $(this).attr('data-title');
    $('.dialogModal_header span').html(dataTitle);
    $.ajax({url:url}).done(function(content){
        $('.dialogModal_content').html(content);
    });
});

$('body').on('change', '#subItem', function(e) {
    var subItem = $(this).val();
    var url = "/modal-sub-item";
    $.ajax({
        url:url ,
        type: 'GET',
        data:'subItem='+subItem,
        success: function(response){
            obj = JSON.parse(response);
            $('#sizeLoad').html(obj['subItem']);
        }
    });
});

$('body').on('click', '.productView', function(e) {

    var url = $(this).attr('data-url');
    var dataTitle = $(this).attr('data-title');
    $('.dialogModal_header').html(dataTitle);
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({url:url}).done(function(content){
                el.find('.dialogModal_content').html(content);
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });
});

$('body').on('click', '.btn-cookie', function(el) {
    var val = $(this).attr('id');
    var url = $(this).attr('data-url');
    $.cookie('productList', val, {path: '/'});
    setTimeout(location.reload(), 1000);
});

$('body').on('click', '.btn-sorted', function(el) {
    $("#showFilter").slideToggle(200);
});

$('body').on( "click", "#filter", function(e){
    $('#productFilter').slideToggle('2000');
    $("span", this).toggleClass("fa-close fa-filter");
});


$('body').on( "click", ".showCartItem", function(e){
    $.ajax({url:'/cart/product-details'}).done(function(content){
        $("#showCartItem").html(content).slideDown("slow");
        $('html, body').animate({
            'scrollTop' : $("#showCartItem").position().top
        }, 1000);
    });
});


$('body').on( "click", ".hideCartItem", function(e){
    $("#showCartItem").slideUp("slow");
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
            $('.cartSubmit').html('<i class="fa fa-shopping-cart"></i> '+qnt+' in Basket');
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
         //   $('#cart-item-list-box').html(obj['cartItem']);
            $('.totalItem').html(obj['totalItem']);
            $('.totalAmount').html(obj['cartTotal']);
            $('.vsidebar .txt').html(obj['cartResult']);

        }
    });
    e.preventDefault();
});

$(document).on( "click", ".btn-number-cart", function(e){

    e.preventDefault();

    url = $(this).attr('data-url');
    productId = $(this).attr('data-text');
    price = $(this).attr('data-title');
    fieldId = $(this).attr('data-id');
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    input = $('#quantity-'+$(this).attr('data-id'));
    currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type === 'minus') {
            if(currentVal > input.attr('min')) {
                var existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                    .done(function( data ) {
                        obj = JSON.parse(data);
                        subTotal = (existVal * parseInt(price));
                        $('#btn-total-'+fieldId).html(subTotal);
                        $('#cart-item-list-box').html(obj['cartItem']);
                        $('.totalItem').html(obj['totalItem']);
                        $('.totalAmount').html(obj['cartTotal']);
                        $('.vsidebar .txt').html(obj['cartResult']);
                    });
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $('#quantity-'+fieldId).attr('disabled', true);
            }else {
                $('#quantity-'+fieldId).attr('disabled', false);
            }

        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                var existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                    .done(function(data){
                        obj = JSON.parse(data);
                        if(obj['process'] === 'success'){
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
            if(parseInt(input.val()) === input.attr('max')) {
                $('#quantity-'+fieldId).attr('disabled', true);
            }else {
                $('#quantity-'+fieldId).attr('disabled', false);
            }

        }
    } else {
        input.val(0);
    }
});

$(document).on( "click", ".btn-cart-item", function(e){

    e.preventDefault();

    url = $(this).attr('data-url');
    productId = $(this).attr('data-text');
    price = $(this).attr('data-title');
    fieldId = $(this).attr('data-id');
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    input = $('#quantity-'+$(this).attr('data-id'));
    currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type === 'minus') {
            if(currentVal > input.attr('min')) {
                var existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                    .done(function( data ) {
                        obj = JSON.parse(data);
                        subTotal = (existVal * parseInt(price));
                        $('#btn-total-'+fieldId).html(subTotal);
                        $('#cart-item-list-box').html(obj['cartItem']);
                        $('.totalItem').html(obj['totalItem']);
                        $('.totalAmount').html(obj['cartTotal']);
                        $('.vsidebar .txt').html(obj['cartResult']);
                    });
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $('#quantity-'+fieldId).attr('disabled', true);
            }else {
                $('#quantity-'+fieldId).attr('disabled', false);
            }

        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                var existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                    .done(function(data){
                        obj = JSON.parse(data);
                        if(obj['process'] === 'success'){
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
            if(parseInt(input.val()) === input.attr('max')) {
                $('#quantity-'+fieldId).attr('disabled', true);
            }else {
                $('#quantity-'+fieldId).attr('disabled', false);
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
                $('#productBuy').addClass('shopping-cart').html('<i class="fa fa-shopping-cart"></i> '+qnt+' in Basket');
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
                $('#buy-'+dataId).addClass('shopping-cart').html('<i class="fa fa-shopping-cart"></i> 1 in Basket');
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
    var input = $("input[name='quantity']");
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

$(document).on( "focusin", ".btn-number", function(e){
    $(this).data('oldValue', $(this).val());
});
$(document).on( "change", ".btn-number", function(e){
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
