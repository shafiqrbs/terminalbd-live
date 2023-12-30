// hide all contents accept from the first div
$('.tabContent div:not(:first)').toggle();

// hide the previous button
$('.previous').hide();

$('.table-tab li').click(function () {

    var id = $(this).data("id");
    if ($(this).is(':last-child')) {
        $('.next').hide();
    } else {
        $('.next').show();
    }

    if ($(this).is(':first-child')) {
        $('.previous').hide();
    } else {
        $('.previous').show();
    }

    var position = $(this).position();
    var corresponding = $(this).data("id");
    var url = $(this).attr("data-action");
    // scroll to clicked tab with a little gap left to show previous tabs
    scroll = $('.tabs').scrollLeft();
    $('.tabs').animate({
        'scrollLeft': scroll + position.left - 30
    }, 200);

    // hide all content divs
    $('.tabContent div').hide();

    // show content of corresponding tab
    $('div.' + corresponding).toggle(
        $.get(url)
            .done(function( response ) {
                obj = JSON.parse(response);
                $('#tableInvoice').val(corresponding);
                $('#invoiceEntity').val(corresponding);
                $('#transaction-box').html(obj['body']);
                $('#process-box').html(obj['htmlProcess']);
                $('.due').html(obj['total']);
            })
    );

    // remove active class from currently not active tabs
    $('.tabs li').removeClass('active');

    // add active class to clicked tab
    $(this).addClass('active');
});

$('#search').keyup(function(){

    // Search text
    var text = $(this).val();

    // Hide all content class element
    $('.product-content').hide();

    // Search
    $('.product-content .cta-title').each(function(){

        if($(this).text().toLowerCase().indexOf(""+text+"") != -1 ){
            $(this).closest('.product-content').show();
        }
    });

});

$(document).on('click', '.js-accordion-title', function() {
    $(this).next().slideToggle(200);
    $(this).toggleClass('open', 200);
});

$('.next').click(function(e){
    e.preventDefault();
    $('ul.table-tab li.active').next('li').trigger('click');
});

$('.previous').click(function(e){
    e.preventDefault();
    $('ul.table-tab li.active').prev('li').trigger('click');
});

$('#btn-refresh').click(function(e){
    $('.payment').val('');
    $('#balance').html(0);
    e.preventDefault();
});

$(document).on( "click", ".btn-number-pos", function(e){

    e.preventDefault();
    url = $(this).attr('data-action');
    var price = $(this).attr('data-title');
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
                $.get( url,{ quantity:existVal})
                    .done(function( response ) {
                        subTotal = (existVal * parseInt(price));
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
                $.get( url,{ quantity:existVal})
                    .done(function( response ) {
                        subTotal = (existVal * parseInt(price));
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

$(document).on('click', '.invoice-process', function() {
    var process = $(this).val();
    var entity = $(this).attr('data-id');
    var url = $(this).attr('data-action');
    if(process ===  "Payment"){
        $('.hide-payment').hide();
    }else{
        $('.hide-payment').show();
    }
    $.get(url,{'process':process}, function( response ) {
        obj = JSON.parse(response);
        $('#process-'+entity).removeClass().addClass(obj['process']).html(obj['process']);
        $('#orderTime-'+entity).html( obj['orderTime']);
        if(obj['process'] === "Free"){
            jsonResult(response);
        }

    });
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
    $('#invoiceParticulars').html(obj['invoiceParticulars']).show();
    $('#subTotal').html(financial(obj['subTotal']));
    $('.total').html(financial(obj['total']));
    $('#total').val(obj['total']);
    $('.vat').html(obj['vat']);
    $('.due').html(financial(obj['total']));
    $('#due').val(obj['total']);
    $('.discount').html(obj['discount']);
    $('#process-'+obj['entity']).removeClass().addClass(obj['process']).html(obj['process']);
    $('#restaurant_invoice_discount').val(obj['discount']);
    if(obj['total'] > 0 ){
        $('.receiveBtn').attr("disabled", false);
    }else{
        $('.receiveBtn').attr("disabled", true);
    }
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
        url         : Routing.generate('restaurant_tableinvoice_update'),
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
        url         : Routing.generate('restaurant_tableinvoice_update'),
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

    if($(this).val() === 0){
        $(this).val("Your");
        slert("okay");
    }

    var payment  = parseInt($('#restaurant_invoice_payment').val()  != '' ? $('#restaurant_invoice_payment').val() : 0 );
    var due  = parseInt($('#due').val()  != '' ? $('#due').val() : 0 );
    var dueAmount = (due - payment);
    if(dueAmount > 0){
        $('#balance').html('Due '+financial(dueAmount));
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return '+financial(balance));
    }

});

$(document).on('click', '#saveButton', function() {

    $('#buttonType').val('saveBtn');
    var table = $('#invoiceEntity').val();
    $.ajax({
        url         : $('form#invoiceForm').attr( 'action' ),
        type        : 'POST',
        data        : new FormData($('form#invoiceForm')[0]),
        processData : false,
        contentType : false,
        beforeSend  : function() {
            $('#saveButton').html("Please Wait...").attr('disabled', 'disabled');
        },
        success : function(response){
            $('form#invoiceForm')[0].reset();
            $('.initialVat').html('');
            $('#subTotal').html('');
            $('.vat').html(0);
            $('.sd').html(0);
            $('.discount').html(0);
            $('#restaurant_invoice_vat').val(0);
            $('#buttonType').val('');
            $('#restaurant_invoice_payment').val('');
            $('#saveButton').html("<i class='icon-save'></i> Save");
            $('.receiveBtn').attr('disabled','disabled');
            $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount, .initialDiscount,#balance').html('');
            $('#invoiceParticulars').hide();
        }
    });
});

$(document).on('click', '#posButton', function() {
    var btn = $('#printMode').val();
    $.ajax({
        url         : $('form#invoiceForm').attr( 'action' ),
        type        : $('form#invoiceForm').attr( 'method' ),
        data        : new FormData($('form#invoiceForm')[0]),
        processData : false,
        contentType : false,
        beforeSend  : function() {
            $('#posButton').html("Please Wait...").attr('disabled', 'disabled');
        },
        success     : function(response){
            $('form#invoiceForm')[0].reset();
            $('.initialVat').html('');
            $('#subTotal').html('');
            $('.vat').html(0);
            $('.sd').html(0);
            $('.discount').html(0);
            $('#restaurant_invoice_vat').val(0);
            $('#restaurant_invoice_payment').val(0);
            $('#posButton').html("<i class='icon-print'></i> POS PRINT");
            $('.receiveBtn').attr('disabled','disabled');
            $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount, .initialDiscount,#balance').html('');
            $('#invoiceParticulars').hide();
            if(btn === 'posBtn'){
                jsPostPrint(response);
            }
            if(btn === 'posDelivery'){
               $('#print-area').html(response).kinziPrint();
            }
        }
    });
});

function calcNumbers(result){
    restaurant_invoice.restaurant_invoice_payment.value = restaurant_invoice.restaurant_invoice_payment.value+result;
    paymentBalance();
}

function calcGroupNumbers(result){
    restaurant_invoice.restaurant_invoice_payment.value = result;
    paymentBalance();
}

function paymentBalance() {
    var payment  = parseInt($('#restaurant_invoice_payment').val()  != '' ? $('#restaurant_invoice_payment').val() : 0 );
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