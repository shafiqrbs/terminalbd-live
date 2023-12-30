$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});

$("form#invoiceForm").on('click', '.addCustomer', function() {
    $( ".customer" ).slideToggle( "slow" );
});

$( "#name" ).autocomplete({

    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('domain_customer_auto_name_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 2,
    select: function( event, ui ) {}
});

$( "#mobile" ).autocomplete({

    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('domain_customer_auto_mobile_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 2,
    select: function( event, ui ) {}
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

$(document).on( "click", ".patientShow", function(e){
    $('#updatePatient').slideToggle(2000);
    $("span", this).toggleClass("fa fa-angle-double-up fa fa-angle-double-down");
});

$(document).on( "click", ".receivePayment", function(e){
    $("#showPayment").slideToggle(1000);
    $("span", this).toggleClass("fa-minus fa-money");
});

$(document).on( "change", ".invoiceSearch", function(e){

    var search = $('.invoiceSearch').val();
    var url = $('.invoiceSearch').attr("data-url");
    $.get(url, function(data){

    });
});


$(document).on('change', '#particular', function() {

    var url = $(this).val();
    if(url == ''){
        alert('You have to add particulars from drop down and this not service item');
        return false;
    }
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#particularId').val(obj['particularId']);
            $('#quantity').val(obj['quantity']).focus();
            $('#price').val(obj['price']);
            $('#instruction').html(obj['instruction']);
            $('#addParticular').attr("disabled", false);
        }
    })
});

$(document).on('click', '#addParticular', function() {

    var particularId = $('#particularId').val();
    var quantity = parseInt($('#quantity').val());
    var price = parseInt($('#price').val());
    var url = $('#addParticular').attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'particularId='+particularId+'&quantity='+quantity+'&price='+price,
        success: function (response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').val(obj['vat']);
            $('.due').html(obj['due']);
            $('#due').val(obj['due']);
            $('.discountAmount').html(obj['discount']);
            $('.discount').val(obj['discount']).attr( "placeholder", obj['discount'] );
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
            $("#particular").select2().select2("val","");
            $('#price').val('');
            $('#quantity').val('1');
            $('#addParticular').attr("disabled", true);
            $('#addPatientParticular').attr("disabled", true);

        }
    })
});

$(document).on('change', '#discountType , #hospitalInvoice_discountCalculation', function() {

    var discount = parseInt($('#hospitalInvoice_discountCalculation').val());
    var invoice = parseInt($('#invoiceId').val());
    var discountType = $('#discountType').val();
    if(discount === "NaN"){
        return false;
    }
    $.ajax({
        url: Routing.generate('hms_invoice_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&invoice='+ invoice+'&discountType='+discountType,
        success: function(response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').html(obj['vat']);
            $('.due').html(obj['due']);
            $('#due').val(obj['due']);
            $('.discountAmount').html(obj['discount']);
            $('#msg').html(obj['msg']);
        }

    })
});

$(document).on("click", ".removeDiscount", function() {
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {location.reload();});
        }
    });
});

$(document).on("click", ".particularDelete", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {location.reload();});
        }
    });
});

$(document).on('click', '#addPayment', function() {

    var payment = $('#payment').val();
    var discount = $('#discount').val();
    var process = $('#process').val();
    var url = $('#addPayment').attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function (event, el) {
            $.ajax({
                url: url,
                type: 'POST',
                data: 'payment=' + payment + '&discount=' + discount + '&process=' + process,
                success: function (response){
                    location.reload();
                }
            })
        }
    });

});

$(document).on("click", "#diagnosticReceiveBtn", function() {

    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('#invoiceForm').submit();
        }
    });

});

$(document).on('keyup', '#hospitalInvoice_payment', function() {

    var payment  = parseInt($('#hospitalInvoice_payment').val()  != '' ? $('#hospitalInvoice_payment').val() : 0 );
    var due  = parseInt($('#due').val()  != '' ? $('#due').val() : 0 );
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

var invoicePayment = $("#invoicePayment").validate({

    rules: {
        "invoicePayment[payment]": {required: true, digits: true},
        "invoicePayment[discount]": {required: false},
        "invoicePayment[cardNo]": {required: false},
        "invoicePayment[paymentCard]": {required: false},
        "invoicePayment[accountBank]": {required: false},
        "invoicePayment[accountMobileBank]": {required: false},
        "invoicePayment[paymentMobile]": {required: false},
        "invoicePayment[transactionId]": {required: false},
        "invoicePayment[comment]": {required: false},
    },
    messages: {
        "invoicePayment[payment]": "Enter payment amount",
    },
    tooltip_options: {
        "invoicePayment[payment]": {placement: 'top', html: true},
    },
    submitHandler: function (invoicePayment) {
        $('form#invoicePayment').submit();
    }
});
