// hide all contents accept from the first div
$('.tabContent div:not(:first)').toggle();

// hide the previous button
$('.previous').hide();

$(".number , .amount").inputFilter(function(value) {
    return /^-?\d*[.,]?\d*$/.test(value); });

function financial(val) {
    return Number.parseFloat(val).toFixed(2);
}

$('.invoice-change').click(function() {
    $(this).attr('value', '');
});

$.ajax({
    url: Routing.generate('pos_stock_item'),
    beforeSend: function(){
        $('.loader-double').fadeIn(1000).addClass('is-active');
    },
    complete: function(){
        $('.loader-double').fadeIn(1000).removeClass('is-active');
    },
    success:  function (data) {
        $("#items").html(data);
        tableSearch();
    }
});

$(document).on('click', '#allproduct', function() {

    $.ajax({
        url: Routing.generate('pos_stock_item'),
        beforeSend: function(){
            $('.loader-double').fadeIn(1000).addClass('is-active');
        },
        complete: function(){
            $('.loader-double').fadeIn(1000).removeClass('is-active');
        },
        success:  function (data) {
            $("#items").html(data);
            tableSearch();
        }
    });
});

$(document).on('click', '.category', function() {

    url = $(this).attr('data-action');
    $.ajax({
        url: url,
        beforeSend: function(){
            $('.loader-double').fadeIn(1000).addClass('is-active');
        },
        complete: function(){
            $('.loader-double').fadeIn(1000).removeClass('is-active');
        },
        success:  function (data) {
            $("#items").html(data);
            tableSearch();
        }
    });
});

$(document).on('click', '.list-load', function() {

    url = $(this).attr('data-action');
    $('.dialogModal_header').html('List Information');
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: url,
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                    modalInnerFunction();
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});

$(document).on('click', '.js-accordion-title', function() {
    $(this).next().slideToggle(200);
    $(this).toggleClass('open', 200);
});

$(".addCustomer").click(function(){
    $( ".customer" ).slideToggle( "slow" );
}).toggle( function() {
    $(this).removeClass("blue").addClass("red").html('<i class="icon-remove"></i>');
}, function() {
    $(this).removeClass("red").addClass("blue").html('<i class="icon-user"></i>');
});

$(document).on( "click", "#btn-refresh", function(e){
    $('.payment').val(0);
    var payment  = parseInt($('#total').val());
    $('#balance').html(financial(payment));
    e.preventDefault();
});

$(document).on('change', '.updateProductPrice', function() {

    var id = $(this).attr('data-id');
    var price = $("#price-"+id).val();
    var quantity = $("#quantity-"+id).val();
    var url = $(this).attr('data-action');
    $.get(url, {quantity: quantity,price: price} , function(response){
            setTimeout(jsonResult(response),100);
    });

});

$(document).on( "click", ".btn-number-pos", function(e){

    e.preventDefault();
    url = $(this).attr('data-action');
    fieldId = $(this).attr('data-id');
    var price = financial($('#price-'+fieldId).val());
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $('#quantity-'+fieldId);
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            if(currentVal > input.attr('min')) {
                var existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,price:price})
                    .done(function( response ) {
                        subTotal = financial(existVal * parseInt(price));
                        $('#subTotal-'+fieldId).html(subTotal);
                        setTimeout(jsonResult(response),100);
                    });
            }
            if(parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
            }

        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                var existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,price:price})
                    .done(function( response ) {
                        subTotal = financial(existVal * parseInt(price));
                        $('#subTotal-'+fieldId).html(subTotal);
                        setTimeout(jsonResult(response),100);
                    });
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
            }

        }
    } else {
        input.val(1);
    }
});

$(document).on( "click", ".btn-number-cart", function(e){

    e.preventDefault();
    url = $(this).attr('data-action');
    fieldId = $(this).attr('data-id');
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $('#quantity-'+fieldId);
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            if(currentVal > input.attr('min')) {
                var existVal = (currentVal - 1);
                input.val(existVal).change();
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $(this).attr('disabled', true);
            }

        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                var existVal = (currentVal + 1);
                input.val(existVal).change();
            }
            if(parseInt(input.val()) === input.attr('max')) {
                $(this).attr('disabled', true);
            }

        }
    } else {
        input.val(1);
    }
});

$(document).on('change', '#barcode', function() {

    var barcode = $('#barcode').val();
    if(barcode === ''){
        $('#wrongBarcode').html('<strong>Error!: </strong>Invalid barcode, Please try again.');
        return false;
    }
    $.ajax({
        url: Routing.generate('pos_item_barcode_search'),
        type: 'POST',
        data:'barcode='+barcode,
        success: function(response){
            $('#barcode').focus().val('');
            jsonResult(response);
        },

    })
});

$(document).on('change', '#barcodeNo', function() {

    var barcode = $('#barcodeNo').val();
    if(barcode == ''){
        $('#wrongBarcode').html('Using wrong barcode, Please try again correct barcode.');
        return false;
    }
    $.ajax({
        url: Routing.generate('pos_item_barcode_search'),
        type: 'POST',
        data:'barcode='+barcode+'&sales='+ sales,
        success: function(response) {
            $('#barcodeNo').focus().val('');
            jsonResult(response);
        },
    })
});

$('#customize-controls').on('click' , function() {
    $('#item').select2("close");
} );

$( "#stockItem" ).submit(function( event ) {

    var stockItem = $('#item').val();
    if(stockItem === ''){
        alert('Please try again correct product.');
        return false;
    }
    $.ajax({
        url         : Routing.generate('pos_item_create'),
        type        : 'POST',
        data        : new FormData($('form#stockItem')[0]),
        processData : false,
        contentType : false,
        success     : function(response){
            $('#quantity').val(1);
            $('#item').select2('open');
            jsonResult(response);
        }
    });
    event.preventDefault();
});

/*
function afterSelect2Submit(){

    $('.select2StockItem').prepend('<option selected></option>').select2({

        placeholder: "Search item,barcode",
        ajax: {
            url: Routing.generate('pos_item_search'),
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
        formatResult: function(item){

            //return item.name +' => '+ (item.remainingQuantity)
            return item.name

        }, // omitted for brevity, see the source of this page
        formatSelection: function(item){return item.name + '(' + item.sku+')'}, // omitted for brevity, see the source of this page
        initSelection: function(element, callback) {
            var id = $(element).val();
        },
        allowClear: true,
        minimumInputLength:1
    });

}
*/

$(document).on('click', '.addToCart', function() {

    var id = $(this).attr('data-id');
    var quantity = $("#quantity-"+id).val();
    var url = $(this).attr('data-action');
    $.get(url, {quantity: quantity} , function(response){
        setTimeout(jsonResult(response),100);
    });
});


$(document).on('click', '.invoice-mode', function() {
    url = $(this).attr('data-action');
    $.get(url, function( response ) {
        location.reload();
    });
});

$(document).on('click', '.invoice-process', function() {

    var process = $(this).val();
    var url = $(this).attr('data-action');
    if(process === 'order'){
        $.get(url, function( response ) {
            $("#invoice").html(data);
        });
    }else if(process === 'cancel'){
        $.get(url, function( response ) {
            resetInvoice();
        });
    }else if(process === 'hold'){
        $.get(url, function( response ) {
            $("#invoice").html(data);
        });
    }else{
        $.ajax({
            url         : url,
            type        : 'POST',
            data        : new FormData($('form#invoiceForm')[0]),
            processData : false,
            contentType : false,
            success     : function(response){
                if(process === 'print'){
                    var salesId = response;
                    window.open('/app-pos/desktop/'+ salesId +'/print', '_blank');
                }else if(process === 'pos'){
                    jsPostPrint(response);
                }
                resetInvoice();
            }
        });
    }
});

function resetInvoice(){
    $('form#invoiceForm')[0].reset();
    $('#invoiceItems').html('');
    $('#balance').html('');
    $('.subTotal').html('');
    $('.total').html('');
    $('.vat').html('');
    $('.due').html('');
    $('.discount').html('');
}

$(".select2StockItem").select2({

    placeholder: "Search item,barcode",
    ajax: {
        url: Routing.generate('pos_item_search'),
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
    formatResult: function(item){

        //return item.name +' => '+ (item.remainingQuantity)
        return item.name

    }, // omitted for brevity, see the source of this page
    formatSelection: function(item){return item.name + '(' + item.sku+')'}, // omitted for brevity, see the source of this page
    initSelection: function(element, callback) {
        var id = $(element).val();
    },
    allowClear: true,
    minimumInputLength:1
});

$("#barcodeNo").select2({

    placeholder: "Enter specific barcode",
    allowClear: true,
    ajax: {
        url: Routing.generate('inventory_purchaseitem_search'),
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
    formatResult: function(item){ return item.text}, // omitted for brevity, see the source of this page
    formatSelection: function(item){return item.text }, // omitted for brevity, see the source of this page
    initSelection: function(element, callback) {
        var id = $(element).val();
    },
    minimumInputLength:1
});

$(".select2Customer").select2({

    ajax: {
        url: Routing.generate('domain_customer_search'),
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
    formatResult: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    formatSelection: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var customer = $(element).val();
        $.ajax(Routing.generate('domain_customer_name', { customer : customer}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 2
});

$(document).on('click', '.method-process', function() {
    var method = $(this).val();
    if(method === 'Bank' ){
        $('.bankHide').show(500);
        $('.mobileBankHide').hide(500);
    }else if(method === 'Mobile'){
        $('.bankHide').hide(500);
        $('.mobileBankHide').show(500);
    }else{
        $('.bankHide').hide(500);
        $('.mobileBankHide').hide(500);
    }
    $.ajax({
        url         : Routing.generate('restaurant_tableinvoice_update'),
        type        : 'POST',
        data        : new FormData($('form#invoiceForm')[0]),
        processData : false,
        contentType : false,
        success     : function(response){

        }
    });
});

function jsonResult(response) {

    obj = JSON.parse(response);
    $('#invoiceItems').html(obj['invoiceItems']).show();
    $('.subTotal').html(financial(obj['subTotal']));
    $('.total').html(financial(obj['total']));
    $('#balance').html(financial(obj['total']));
    $('#total').val(obj['total']);
    $('.vat').html(obj['vat']);
    $('.due').html(financial(obj['total']));
    $('#due').val(obj['total']);
    $('.discount').html(obj['discount']);
    $('#process-'+obj['entity']).removeClass().addClass(obj['process']).html(obj['process']);
    $('#post_discount').val(obj['discount']);
}


$(document).on('click', '.addProduct', function() {

    var invoice = $('#tableInvoice').val();
    var url = $(this).attr('data-action');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url,{'invoice':invoice}, function( response ) {
                setTimeout(jsonResult(response),100);
            });
        }
    });
});

$(document).on('click', '.event', function(event) {

    var url = $(this).attr('data-action');
    var id = $(this).attr('data-id');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( response ) {
                obj = JSON.parse(response);
                $('#remove-'+id).hide();
                if( obj['success'] === 'success'){
                    setTimeout(jsonResult(response),100);
                }
            });
        }
    });
});

$(document).on("click", ".initialParticularDelete , .particularDelete", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( response ) {
                setTimeout(jsonResult(response),100);
                $("#remove-"+id).remove();
            });
        }
    });
});

$(document).on('click', '.invoice-input', function(e) {

    $.ajax({
        url         : Routing.generate('inventory_pos_update'),
        type        : 'POST',
        data        : new FormData($('form#invoiceForm')[0]),
        processData : false,
        contentType : false,
        success     : function(response){
            setTimeout(jsonResult(response),100);
        }
    });
    e.preventDefault();
});

$(document).on('change', '.invoice-change', function(e) {

    $.ajax({
        url         : Routing.generate('pos_update'),
        type        : 'POST',
        data        : new FormData($('form#invoiceForm')[0]),
        processData : false,
        contentType : false,
        success     : function(response){
            setTimeout(jsonResult(response),100);
        }
    });
    e.preventDefault();
});

$(document).on('click', '#posKitchen', function(e) {
    url = $(this).attr('data-action');
    var atLeastOneIsChecked =$('input[name="isPrint[]"]:checked').length > 0;
    var searchIDs = $('input[name="isPrint[]"]:checked').map(function(){
        return $(this).val();
    }).get();
    if(atLeastOneIsChecked === true){
        $.get(url,{'isPrint':searchIDs}, function(response) {
            jsPostPrint(response);
        });
    }
    e.preventDefault();

});

function financial(val) {
    return Number.parseFloat(val).toFixed(2);
}

// Install input filters.
$(".input-number").inputFilter(function(value) {
    return /^-?\d*$/.test(value); });

$(document).on('keyup', '.payment', function() {

    var payment  = parseInt($('#pos_payment').val()  !== '' ? $('#pos_payment').val() : 0 );
    var due  = parseInt($('#due').val()  !== '' ? $('#due').val() : 0 );
    var dueAmount = (due - payment);
    if(dueAmount > 0){
        $('#balance').html('Due '+financial(dueAmount));
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return '+financial(balance));
    }

});

function calcNumbers(result){
    invoiceForm.pos_payment.value = invoiceForm.pos_payment.value+result;
    paymentBalance();
}

function calcGroupNumbers(result){
    invoiceForm.pos_payment.value = result;
    paymentBalance();
}

function paymentBalance() {
    var payment  = parseInt($('#pos_payment').val()  != '' ? $('#pos_payment').val() : 0 );
    var due  = parseInt($('#due').val()  != '' ? $('#due').val() : 0 );
    var dueAmount = (due - payment);
    if(dueAmount > 0){
        $('#balance').html('Due '+financial(dueAmount));
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return '+financial(balance));
    }
}

$(document).on("click", "#kitchenBtn", function() {
    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function(response) {
                //jsPostPrint(response);
            });
        }
    });
});

function modalInnerFunction() {
    $('.toggle').click(function(){
        var id = $(this).attr('id');
        $("#show-"+id).slideToggle(100);
    }).toggle( function() {
        $(this).children("span").text("[ - Close ]");
    }, function() {
        $(this).children("span").text("[ + Show ]");
    });
}
function  tableSearch() {

    $('#dataTableSearch').filterTable({ // apply filterTable to all tables on this page
        label:'',
        inputName:'search',
        containerClass: 'search',
        placeholder: 'Enter table keywords'
    });

    $('#search').keyup(function(){
        var text = $(this).val();
        $('.product-content').hide();
        $('.product-content .cta-title').each(function(){
            if($(this).text().toLowerCase().indexOf(""+text+"") != -1 ){
                $(this).closest('.product-content').show();
            }
        });

    });

}
function jsPostPrint(data) {
    if(typeof EasyPOSPrinter == 'undefined') {
        alert("Printer library not found");
        return;
    }
    EasyPOSPrinter.raw(data);
    EasyPOSPrinter.cut();
    EasyPOSPrinter.print(function(r, x){
        console.log(r)
    });
}