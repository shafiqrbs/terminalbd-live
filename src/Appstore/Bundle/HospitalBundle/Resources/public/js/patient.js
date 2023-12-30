$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});

$(document).on('click', '.addPatient', function() {

    $('.dialogModal_header').html('Patient Information');
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: Routing.generate('hms_invoice_temporary_new'),
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                    formSubmit();
                    $('.select2').select2();
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});

$(document).on("click", ".saveButton", function() {

    var formData = new FormData($('form#invoiceForm')[0]); // Create an arbitrary FormData instance
    var url = $('form#invoiceForm').attr('action'); // Create an arbitrary FormData instance
    $.ajax({
        url:url ,
        type: 'POST',
        processData: false,
        contentType: false,
        data:formData,
        success: function(response){

        }
    });

});

function formSubmit() {

  /*  $('#saveDiagnosticButton').attr("disabled", true);*/

    $("form#invoicePatientForm").on('click', '.addCustomer', function() {
        $( ".customer" ).slideToggle( "slow" );
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

    $(document).on('click', '#temporaryParticular', function() {

        var particularId = $('#particularId').val();
        var quantity = parseInt($('#quantity').val());
        var price = parseInt($('#price').val());
        var url = $('#temporaryParticular').attr('data-url');
        if(particularId == ''){
            $("#particular").select2('open');
            return false;
        }
        $.ajax({
            url: url,
            type: 'POST',
            data: 'particularId='+particularId+'&quantity='+quantity+'&price='+price,
            success: function (response) {
                obj = JSON.parse(response);
                $('#invoiceParticulars').show();
                $('.subTotal').html(obj['subTotal']);
                $('.initialGrandTotal').html(obj['initialGrandTotal']);
                $('#initialDue').val(obj['initialGrandTotal']);
                $('#invoiceParticulars').html(obj['invoiceParticulars']);
                $('.msg-hidden').show();
                $('#msg').html(obj['msg']);
                $("#particular").select2().select2("val","").select2('open');
                $('#price').val('');
                $('#quantity').val('1');
                $('#particularId').val('');
                $('#addParticular').attr("disabled", true);
                if(obj['initialGrandTotal'] > 0 ){
                    $('#saveDiagnosticButton').attr("disabled", false);
                }else{
                    $('#saveDiagnosticButton').attr("disabled", true);
                }
            }
        })
    });

    $(document).on('change', '#discountType , #appstore_bundle_hospitalbundle_invoice_discountCalculation', function() {

        var discountType = $('#discountType').val();
        var discount = parseInt($('#appstore_bundle_hospitalbundle_invoice_discountCalculation').val());
        if(discount === "NaN"){
            return false;
        }
        $.ajax({
            url: Routing.generate('hms_invoice_temporary_discount_update'),
            type: 'POST',
            data:'discount=' + discount+'&discountType='+discountType,
            success: function(response) {
                obj = JSON.parse(response);
                $('.subTotal').html(obj['subTotal']);
                $('.initialGrandTotal').html(obj['initialGrandTotal']);
                $('.initialDiscount').html(obj['initialDiscount']);
                $('#initialDiscount').val(obj['initialDiscount']);
                $('#appstore_bundle_hospitalbundle_invoice_discount').val(obj['initialDiscount']);
                $('#initialDue').val(obj['initialGrandTotal']);

            }

        })
    });

    $(document).on("click", ".initialParticularDelete , .particularDelete", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get(url, function( data ) {
                    obj = JSON.parse(data);
                    $('.subTotal').html(obj['subTotal']);
                    $('.initialGrandTotal').html(obj['initialGrandTotal']);
                    $('#initialDue').val(obj['initialGrandTotal']);
                    $('.due').html(obj['due']);
                    $('.discountAmount').html(obj['discount']);
                    $('.discount').val('').attr( "placeholder", obj['discount'] );
                    $('#appstore_bundle_hospitalbundle_invoice_discount').val(obj['discount']);
                    $('.total'+id).html(obj['total']);
                    $('#msg').html(obj['msg']);
                    $('#remove-'+id).hide();
                    if(obj['initialGrandTotal'] > 0 ){
                        $('#saveDiagnosticButton').attr("disabled", false);
                    }else{
                        $('#saveDiagnosticButton').attr("disabled", true);
                    }
                });
            }
        });
    });

    $(document).on('keyup', '.payment', function() {

        var payment  = parseInt($('#appstore_bundle_hospitalbundle_invoice_payment').val()  != '' ? $('#appstore_bundle_hospitalbundle_invoice_payment').val() : 0 );
        var due  = parseInt($('#initialDue').val()  != '' ? $('#initialDue').val() : 0 );
        var dueAmount = (due - payment);
        if(dueAmount > 0){
            $('#balance').html('Due Tk.');
            $('#dueable').html(dueAmount);
        }else{
            var balance =  payment - due ;
            $('#balance').html('Return Tk.');
            $('#dueable').html(balance);
        }

    });
    var form = $("#invoicePatientForm").validate({

        rules: {

            "appstore_bundle_hospitalbundle_invoice[customer][name]": {required: true},
            "appstore_bundle_hospitalbundle_invoice[customer][mobile]": {required: true, digits: true},
            "appstore_bundle_hospitalbundle_invoice[customer][age]": {required: true, digits: true},
            "appstore_bundle_hospitalbundle_invoice[discountCalculation]": {required: false, digits: true},
            "appstore_bundle_hospitalbundle_invoice[payment]": {required: true, digits: true},
            "appstore_bundle_hospitalbundle_invoice[customer][address]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[customer][location]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[referredDoctor][name]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[referredDoctor][mobile]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[comment]": {required: false},
        },

        messages: {
            "appstore_bundle_hospitalbundle_invoice[customer][name]": "Enter patient name",
            "appstore_bundle_hospitalbundle_invoice[customer][mobile]": "Enter patient mobile no",
            "appstore_bundle_hospitalbundle_invoice[customer][age]": "Enter patient age",
            "appstore_bundle_hospitalbundle_invoice[payment]": "Enter payment amount, if payment are due input zero",
        },
        tooltip_options: {
            "appstore_bundle_hospitalbundle_invoice[customer][name]": {placement: 'top', html: true},
            "appstore_bundle_hospitalbundle_invoice[customer][mobile]": {placement: 'top', html: true},
            "appstore_bundle_hospitalbundle_invoice[customer][age]": {placement: 'top', html: true},
            "appstore_bundle_hospitalbundle_invoice[payment]": {placement: 'top', html: true},
        },
        submitHandler: function (form) {
            $.ajax({
                url         : $('form#invoicePatientForm').attr( 'action' ),
                type        : $('form#invoicePatientForm').attr( 'method' ),
                data        : new FormData($('form#invoicePatientForm')[0]),
                processData : false,
                contentType : false,
                beforeSend: function() {
                    $('#saveDiagnosticButton').html("Please Wait...").attr('disabled', 'disabled');
                },
                success: function(response){
                    $('form#invoicePatientForm')[0].reset();
                    $('#saveDiagnosticButton').html("<i class='icon-save'></i> Save").attr('disabled', 'disabled');
                    $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount').html('');
                    $('#invoiceParticulars').hide();
                    $("#appstore_bundle_hospitalbundle_invoice_assignDoctor").select2().select2("val","");
                    $("#referredId").select2().select2("val","");
                    window.open('/hms/invoice/'+response+'/print', '_blank');
                }
            });

        }
    });
}



