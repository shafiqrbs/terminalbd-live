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


/*$('body').on('click', '.btn-cookie', function(el) {
    var val = $(this).attr('id');
    var url = $(this).attr('data-url');
    $.cookie('productList', val, {path: '/'});
    setTimeout(location.reload(), 1000);
});*/




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
            $('.totalItem').html(obj['items']);
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
    $.ajax({
        url:url ,
        type: 'GET',
        success: function(response){
            obj = JSON.parse(response);
            $('.totalItem').html(obj['items']);
            $('.totalAmount').html(obj['cartTotal']);
            $('.vsidebar .txt').html(obj['cartResult']);
            $(e.target).closest('tr').hide();

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
            $('#product-modal').modal('show');
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


$(document).on( "click", "#productBuy", function(e){


    var url = $('#productBuy').attr("data-url");
    var data = $('.addCart').serialize();
    var qnt = $('#quantity').val();

    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.post(url,data).done(function(response) {
                obj = JSON.parse(response);
                $('#productBuy').addClass('shopping-cart').attr("disabled", true).html('<i class="fa fa-shopping-cart"></i> '+qnt+' in Basket');
                $('.totalItem').html(obj['items']);
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


$(document).on( "click", "#spec", function(e){
    $('#showSpec').slideToggle('2000');
    $("span", this).toggleClass("glyphicon-chevron-down glyphicon-chevron-up");
});


$('.input-number').focusin(function(){
    $(this).data('oldValue', $(this).val());
});


$(document).on( "click", ".btn-number", function(e){

    e.preventDefault();

    dataId = $(this).attr('data-id');
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $("input[name='"+fieldName+"']");
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type === 'minus') {

            if(currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $(this).attr('disabled', true);
                $('#btn-left-'+dataId).hide();
            }
        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
                $('#btn-left-'+dataId).show();

            }
            if(parseInt(input.val()) === input.attr('max')) {
                $(this).attr('disabled', true);
            }
        }
    } else {
        input.val(0);
    }
});


$(document).on( "click", ".btn-new-cart-item", function(e){

    e.preventDefault();
    productId      = $(this).attr('data-id');
    url         = $(this).attr('data-url');
    fieldName   = $(this).attr('data-field');
    type        = $(this).attr('data-type');
    input = $("input[name='"+fieldName+"']");
    currentVal = parseInt(input.val()) ? parseInt(input.val()) : 0;
    if (!isNaN(currentVal)) {
        if(type === 'minus') {
            if(currentVal > input.attr('min')) {
                existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:-1})
                    .done(function( data ) {
                        obj = JSON.parse(data);
                        $('#cart-item-list-box').html(obj['cartItem']);
                        $('.totalItem').html(obj['items']);
                        $('.totalAmount').html(obj['cartTotal']);
                        $('.vsidebar .txt').html(obj['cartResult']);
                    });
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $(input).attr('disabled', true);
                $('#btn-left-'+productId).hide();
            }else {
                $(input).attr('disabled', false);

            }

        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:1})
                    .done(function(data){
                        obj = JSON.parse(data);
                        if(obj['process'] === 'success'){
                            $('#cart-item-list-box').html(obj['cartItem']);
                            $('.totalItem').html(obj['items']);
                            $('.totalAmount').html(obj['cartTotal']);
                            $('.vsidebar .txt').html(obj['cartResult']);
                        }else{
                            input.val(existVal-1).change();
                            alert('There is not enough product in stock at this moment')
                        }
                    });
            }
            if(parseInt(input.val()) === input.attr('max')) {
                $(input).attr('disabled', true);
            }else {
                $(input).attr('disabled', false);
                $('#btn-left-'+productId).show();
            }

        }
    } else {
        input.val(0);
    }
});

$(document).on( "click", ".btn-new-cart-item-modal", function(e){

    e.preventDefault();
    productId      = $(this).attr('data-id');
    url         = $(this).attr('data-url');
    fieldName   = $(this).attr('data-field');
    type        = $(this).attr('data-type');
    input = $("input[name='"+fieldName+"']");
    currentVal = parseInt(input.val()) ? parseInt(input.val()) : 0;
    if (!isNaN(currentVal)) {
        if(type === 'minus') {
            if(currentVal > input.attr('min')) {
                existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal})
                    .done(function( data ) {
                        obj = JSON.parse(data);
                        $('#cart-item-list-box').html(obj['cartItem']);
                        $('.totalItem').html(obj['items']);
                        $('.totalAmount').html(obj['cartTotal']);
                        $('.vsidebar .txt').html(obj['cartResult']);
                    });
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $(input).attr('disabled', true);
                $('#btn-left-modal').hide();
            }else {
                $(input).attr('disabled', false);

            }

        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal})
                    .done(function(data){
                        obj = JSON.parse(data);
                        if(obj['process'] === 'success'){
                            $('#cart-item-list-box').html(obj['cartItem']);
                            $('.totalItem').html(obj['items']);
                            $('.totalAmount').html(obj['cartTotal']);
                            $('.vsidebar .txt').html(obj['cartResult']);
                        }else{
                            input.val(existVal-1).change();
                            alert('There is not enough product in stock at this moment')
                        }
                    });
            }
            if(parseInt(input.val()) === input.attr('max')) {
                $(input).attr('disabled', true);
            }else {
                $(input).attr('disabled', false);
                $('#btn-left-modal').show();
            }

        }
    } else {
        input.val(0);
    }
});


