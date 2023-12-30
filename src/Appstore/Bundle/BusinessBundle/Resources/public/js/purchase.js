/**
 * Created by rbs on 5/1/17.
 */
$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});

$(document).on('change', '.transactionMethod', function() {

    var paymentMethod = $(this).val();

    if( paymentMethod == 2){
        $('#cartMethod').show();
        $('#bkashMethod').hide();
    }else if( paymentMethod == 3){
        $('#bkashMethod').show();
        $('#cartMethod').hide();
    }else{
        $('#cartMethod').hide();
        $('#bkashMethod').hide();
    }

});

$(document).on('change', '#particular', function() {

    var url = $('#particular').val();
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#quantity').focus();
            $('#particularId').val(obj['particularId']);
           // $('#quantity').val(obj['quantity']);
            $('#price').val(obj['price']);
            $('#purchasePrice').val(obj['purchasePrice']);
            $('#unitPrice').val(obj['purchasePrice']);
            $('#unit').html(obj['unit']);
        }
    })
});


$(document).on('keypress', '#quantity', function() {
    var quantity = parseFloat($(this).val());
    var price = parseFloat($('#price').val());
    var purchasePrice = parseFloat(quantity * price);
    if(purchasePrice != 'NaN' && purchasePrice > 0){
        $('#purchasePrice').val(purchasePrice);
    }
});


/*
$('#particular').on("select2-selecting", function (e) {
    setTimeout(function () {
        $('#particular').select2('open');
    }, 500)
});
*/

$('form#purchaseItem').on('keypress', '.stockInput', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);

        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {

            case 'particular':
                $('#quantity').focus();
                break;

            case 'quantity':
                $('#bonusQuantity').focus();
                break;

            case 'bonusQuantity':
                $('#addParticular').click();
                $('#particular').select2('open');
                break;

        }
        return false;
    }
});
$('#purchasePrice').click(function() {
    $(this).attr('value', '');
});

$(document).on('change', '#barcode', function() {

    var barcode = $('#barcode').val();
    var invoiceId = $('#invoiceId').val();
    if(barcode === ''){
        $('#wrongBarcode').html('Using wrong barcode, Please try again correct barcode.');
        return false;
    }
    $.ajax({
        url: Routing.generate('business_purchase_particular_barcode'),
        type: 'POST',
        data:'barcode='+barcode+'&invoice='+ invoiceId,
        success: function(response) {
            $('#barcode').focus().val('');
            obj = JSON.parse(response);
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('#subTotal').html(obj['subTotal']);
            $('#netTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#discount').html(obj['discount']);
            $('#due').html(obj['due']);
        },
    })
});

var form = $("#purchaseItem").validate({

    rules: {

        "particular": {required: true},
        "purchasePrice": {required: false},
        "quantity": {required: false},
    },

    messages: {

        "particular":"Enter particular name",
        "purchasePrice":"Enter purchase price",
        "quantity":"Enter purchase quantity",
    },
    tooltip_options: {
        "particular": {placement:'top',html:true},
        "purchasePrice": {placement:'top',html:true},
        "quantity": {placement:'top',html:true},
    },

    submitHandler: function(form) {

        $.ajax({
            url         : $('form#purchaseItem').attr( 'action' ),
            type        : $('form#purchaseItem').attr( 'method' ),
            data        : new FormData($('form#purchaseItem')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                obj = JSON.parse(response);
                $('#invoiceParticulars').html(obj['invoiceParticulars']);
                $('#subTotal').html(obj['subTotal']);
                $('#netTotal').html(obj['netTotal']);
                $('#paymentTotal').val(obj['netTotal']);
                $('#discount').html(obj['discount']);
                $('#due').html(obj['due']);
                $('#purchasePrice').val('');
                $('#particular').select2('open');
                $('form#purchaseItem')[0].reset();
                $('#unit').html('Unit');
                $('#quantity').val('');

            }
        });
    }
});

$(document).on('change', '.itemUpdate', function() {

    var id = $(this).attr('data-id');
    var quantity = parseFloat($('#quantity-'+id).val());
    var price = parseFloat($('#price-'+id).val());
    var bonusQuantity = parseFloat($('#bonusQuantity-'+id).val());
    var totalQuantity  = (quantity + bonusQuantity);
    var subTotal  = (quantity * price);
    $("#subTotal-"+id).html(subTotal);
    $("#totalQuantity-"+id).html(totalQuantity);
    if(id > 0){
        $.ajax({
            url: Routing.generate('business_purchase_item_update'),
            type: 'POST',
            data:'purchaseItem='+ id +'&quantity='+ quantity +'&price='+ price+'&bonusQuantity='+ bonusQuantity,
            success: function(response) {
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('#netTotal').html(obj['netTotal']);
                $('#paymentTotal').val(obj['netTotal']);
                $('#discount').html(obj['discount']);
                $('#due').html(obj['due']);

            },

        })
    }

});


/*$(document).on('click', '#addParticular', function() {

    var particularId = $('#particularId').val();
    var quantity = $('#quantity').val();
    var price = $('#purchasePrice').val();
    var url = $('#addParticular').attr('data-url');
    if(particularId == ''){
        $('.msg-hidden').show();
        $('input[name=particular]').focus();
        $('#msg').html('Please select medicine or accessories name');
        return false;
    }
    if(price == ''){
        $('.msg-hidden').show();
        $('#msg').html('Please enter purchase price');
        $('input[name=purchasePrice]').focus();
        return false;
    }
    $.ajax({
        url: url,
        type: 'POST',
        data: 'particularId='+particularId+'&quantity='+quantity+'&price='+price,
        success: function (response) {
            obj = JSON.parse(response);
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('#subTotal').html(obj['subTotal']);
            $('#netTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#discount').html(obj['discount']);
            $('#due').html(obj['due']);
            $('#purchasePrice').val('');
            $("#particular").select2().select2("val","");
            $('#price').val('');
            $('#unit').html('Unit');
            $('#quantity').val('1');
        }
    })
});*/

$(document).on('change', '#purchase_discountCalculation , #purchase_discountType', function() {

    var discountType = $('#purchase_discountType').val();
    var discount = parseInt($('#purchase_discountCalculation').val());
    var invoice = $('#purchaseId').val();
    var total =  parseInt($('#purchase_payment').val());
    if( discount >= total ){
        $('#purchase_discount').val(0);
        return false;
    }
    $.ajax({
        url: Routing.generate('business_purchase_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&discountType='+discountType+'&invoice='+invoice,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#netTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#discount').html(obj['discount']);
            $('#due').html(obj['due']);
        }

    })

});

$('#invoiceParticulars').on("click", ".delete", function() {

    var url = $(this).attr("data-url");
    var id = $(this).attr("id");
    $('#remove-'+id).hide();
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#netTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#discount').html(obj['discount']);
            $('#due').html(obj['due']);

        }
    })
});

$(document).on('keyup', '#purchase_payment', function() {

    var payment     = parseInt($('#purchase_payment').val()  != '' ? $('#purchase_payment').val() : 0 );
    var due =  parseInt($('#paymentTotal').val());
    var dueAmount = (due - payment);
    if(dueAmount > 0){
        $('#balance').html('Due Tk.');
        $('.due').html(dueAmount);
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return Tk.');
        $('.due').html(balance);
    }
});

$('form#purchase').on('keypress', 'input', function (e) {

    if (e.which == 13) {
        e.preventDefault();

        switch (this.id) {

            case 'purchase_discountCalculation':
                $('#purchase_payment').focus();
                break;

            case 'purchase_payment':
                $('#receiveBtn').focus();
                break;
        }
    }
});
