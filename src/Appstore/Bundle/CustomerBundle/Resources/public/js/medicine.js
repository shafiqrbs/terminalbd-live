var pathname = window.location.pathname;
url = pathname.split("/")[2];
$(".select2StockMedicine").select2({

    placeholder: "Search stock product name",

    ajax: {
        url: "/customer/"+url+"/order/order-stock-product-search",
        dataType: 'json',
        delay: 250,
        data: function (params, page) {
            return {
                q: params,
                page_limit: 100
            };
        },
        results: function (data, page) {
            return {
                results: data
            };
        },
        cache: true
    },
    escapeMarkup: function (m) {
        return m;
    },
    formatResult: function (item) { return item.text}, // omitted for brevity, see the source of this page
    formatSelection: function (item) { return item.text }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var id = $(element).val();
    },
    allowClear: true,
    minimumInputLength: 1

});


$(document).on('change', '#orderItem_itemName', function() {

    var item = $(this).val();
    $.ajax({
        url: "/customer/"+url+"/order/order-stock-item-search?id="+item,
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#orderItem_price').val(obj['price']);
            $('#unit').html(obj['unit']);
        }
    })

});

$(document).on('click', '#addOrderItem', function() {
    if($("#orderItem").valid()){
        $.ajax({
            url         : $('form#orderItem').attr( 'action' ),
            type        : $('form#orderItem').attr( 'method' ),
            data        : new FormData($('form#orderItem')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                location.reload();
            }
        });
    }
});

var formTemporary = $("#orderItem").validate({

    rules: {

        "orderItem[itemName]": {required: true},
        "orderItem[price]": {required: true},
        "orderItem[quantity]": {required: true},
    },

    messages: {

        "orderItem[itemName]":"Enter medicine name",
        "orderItem[price]":"Enter sales price",
        "orderItem[quantity]":"Enter medicine quantity",
    },
    tooltip_options: {
        "orderItem[itemName]": {placement:'top',html:true},
        "orderItem[price]": {placement:'top',html:true},
        "orderItem[quantity]": {placement:'top',html:true},
    }
});

function financial(val) {
    return Number.parseFloat(val).toFixed(2);
}

$(document).on("click", ".addCart", function() {
    var id = $(this).attr("id");
    var url = $(this).attr("data-url");
    var color = $("#color-"+id).val();
    var quantity = $("#quantity-"+id).val();
    $.ajax({
        url: url,
        type: 'POST',
        data:'color='+color+'&quantity='+quantity,
        success: function (response) {
            $('.cart span').html(response);
        },
    })

});

$(document).on( "click", ".cartSubmit", function(e){

    url = $(this).attr("data-url");
    id = $(this).attr("data-id");
    price = $(this).attr("data-price");
    quantity = $('#quantity-'+id).val();
    input = $('#quantity-'+$(this).attr('data-id'));
    quantity = parseInt(input.val()) ? parseInt(input.val()) : 0;
    subTotal =  (price * quantity);
    $('#subTotal-'+id).html(financial(subTotal));
    $.ajax({
        url:url ,
        type: 'POST',
        data:'quantity='+quantity,
        success: function(response){
            obj = JSON.parse(response);
            $('.totalItem').html(obj['items']);
            $('.totalAmount').html(obj['cartTotal']);
            $('.dropdown-cart').html(obj['salesItem']);
            $('.vsidebar .txt').html(obj['cartResult']);

        }
    });
    e.preventDefault();

});

$(document).on('click', '.itemRemove', function(e) {

    url = $(this).attr("data-url");
    id = $(this).attr("data-id");
    $.get(url, function( response ) {
        $(e.target).closest('li').hide();
        obj = JSON.parse(response);
        $('.totalItem').html(obj['items']);
        $('.totalAmount').html(obj['cartTotal']);
        $('.dropdown-cart').html(obj['salesItem']);
        $('.vsidebar .txt').html(obj['cartResult']);
    });

});

$(document).on('click', '.cartRemove', function(e) {

    url = $(this).attr("data-url");
    $.get(url, function( data ) {
       location.reload();
    });

});


