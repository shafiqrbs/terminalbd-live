$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});

$(document).on('click', '.addInvoice', function() {

    $('.dialogModal_header').html('Order Information');
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: Routing.generate('restaurant_temporary_new'),
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

$(".addTemporaryCustomer").click(function(){
    $( ".customer" ).slideToggle( "slow" );
}).toggle( function() {
    $(this).removeClass("blue").addClass("red").html('<i class="icon-remove"></i>');
}, function() {
    $(this).removeClass("red").addClass("blue").html('<i class="icon-user"></i>');
});

function formSubmit() {


    $(".addCustomer").click(function(){
        $( ".customer" ).slideToggle( "slow" );
    }).toggle( function() {
        $(this).removeClass("blue").addClass("red").html('<i class="icon-remove"></i>');
    }, function() {
        $(this).removeClass("red").addClass("blue").html('<i class="icon-user"></i>');
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
        minimumInputLength: 1
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


    $(document).on('change', '.particular', function() {
        var id = $(this).val();
        if(id == ''){
            alert('You have to add product from drop down and this not service item');
            return false;
        }
        $.ajax({
            url: Routing.generate('restaurant_temporary_particular_search',{'id':id}),
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

    $('form#particularForm').on('keypress', 'input,select,textarea', function (e) {
        if (e.which == 13) {
            e.preventDefault();
            switch (this.id) {
                case 'quantity':
                    $('#temporaryParticular').trigger('click');
                    break;
            }
        }
    });

    $(document).on('click', '#temporaryParticular', function() {

        var particularId = $('#particularId').val();
        var quantity = parseInt($('#quantity').val());
        var price = parseInt($('#price').val());
        var url = $('#temporaryParticular').attr('data-url');
        if(particularId == ''){
            $("#restaurant_particular_particular").select2('open');
            return false;
        }
        $.ajax({
            url: url,
            type: 'POST',
            data: 'particularId='+particularId+'&quantity='+quantity+'&price='+price,
            success: function (response) {
                setTimeout(jsonResult(response),100);
            }
        })
    });

    $(document).on('click', '.addProduct', function() {

        var url = $(this).attr('data-action');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get(url, function( response ) {
                    setTimeout(jsonResult(response),100);
                });
            }
        });
    });

    $(document).on('change', '#restaurant_invoice_discountType , #restaurant_invoice_discountCalculation', function() {

        var discountType = $('#restaurant_invoice_discountType').val();
        var discount = parseInt($('#restaurant_invoice_discountCalculation').val());
        if(discount === "NaN"){
            return false;
        }
        $.ajax({
            url: Routing.generate('restaurant_temporary_discount_update'),
            type: 'POST',
            data:'discount=' + discount +'&discountType='+ discountType,
            success: function(response) {
                setTimeout(jsonResult(response),100);
            }
        })
    });

    $(document).on('change', '#restaurant_invoice_discountCoupon', function() {

        var discount = $('#restaurant_invoice_discountCoupon').val();
        if(discount === "NaN"){
            return false;
        }
        $.ajax({
            url: Routing.generate('restaurant_temporary_discount_coupon'),
            type: 'POST',
            data:'discount=' + discount,
            success: function(response) {
                setTimeout(jsonResult(response),100);
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
                $.get(url, function( response ) {
                    setTimeout(jsonResult(response),100);
                    $("#remove-"+id).remove();
                });
            }
        });
    });

    $(document).on('keyup', '.payment', function() {

        var payment  = parseInt($('#restaurant_invoice_payment').val()  != '' ? $('#restaurant_invoice_payment').val() : 0 );
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

    $(document).on('click', '#saveButton', function() {
        $('#buttonType').val('saveBtn');
        $.ajax({
            url         : $('form#invoiceForm').attr( 'action' ),
            type        : 'POST',
            data        : new FormData($('form#invoiceForm')[0]),
            processData : false,
            contentType : false,
            beforeSend  : function() {
                $('#saveButton').html("Please Wait...").attr('disabled', 'disabled');
            },
            success     : function(response){
                $('form#invoiceForm')[0].reset();
                $('.initialVat').html('');
                $('#subTotal').html('');
                $('#restaurant_invoice_vat').val(0);
                $('#restaurant_invoice_payment').val('');
                $('#buttonType').val('');
                $('#saveButton').html("<i class='icon-save'></i> Save");
                $('.receiveBtn').attr('disabled','disabled');
                $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount').html('');
                $('#invoiceParticulars').hide();
            }
        });
    });

    $(document).on('click', '#posButton', function() {
        var printMode = $('#printMode').val();
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
                $('#restaurant_invoice_vat').val(0);
                $('#restaurant_invoice_payment').val(0);
                $('#posButton').html("<i class='icon-print'></i> POS PRINT");
                $('.receiveBtn').attr('disabled','disabled');
                $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount').html('');
                $('#invoiceParticulars').hide();
                if(printMode === "posPrint" && response != "success"){
                    jsPostPrint(response);
                }
                if(printMode === "deliveryPrint" && response != "success"){
                    // $('#print-area').html(response).kinziPrint();
                    htmlPrint(response);
                }

            }
        });
    });

    function htmlPrint(response) {
        w = window.open(window.location.href,"_blank");
        w.document.open();
        w.document.write(response);
        w.window.print();

    }

    $('form#invoiceForm').on('keypress', 'input,select,textarea', function (e) {

        if (e.which == 13) {
            e.preventDefault();
            switch (this.id) {
                case 'restaurant_invoice_slipNo':
                    $('#restaurant_invoice_tokenNo').focus();
                    break;
                case 'restaurant_invoice_tokenNo':
                    $('#restaurant_invoice_salesBy').focus();
                    break;
                case 'restaurant_invoice_salesBy':
                    $('#restaurant_invoice_discountCalculation').focus();
                    break;
                case 'restaurant_invoice_discountCalculation':
                    $('#restaurant_invoice_discountCoupon').focus();
                    break;
                case 'restaurant_invoice_discountCoupon':
                    $('#restaurant_invoice_payment').focus();
                    break;

            }
        }
    });

    $(document).on('change', '.updateProduct', function() {

        url = $(this).attr('data-action');
        var price = $(this).attr('data-title');
        fieldId = $(this).attr('data-id');
        quantity = $(this).val();
        $.get( url,{ quantity:quantity})
            .done(function( response ) {
                subTotal = (quantity * parseInt(price));
                $('#subTotal-'+fieldId).html(subTotal);
                setTimeout(jsonResult(response),100);
            });

    });

    $(document).on( "click", ".btn-number", function(e){

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

    function jsonResult(response) {

        obj = JSON.parse(response);
        $('#invoiceParticulars').html(obj['invoiceParticulars']).show();
        $('#subTotal').html(obj['subTotal']);
        $('.initialGrandTotal').html(obj['initialGrandTotal']);
        $('#initialDue').val(obj['initialGrandTotal']);
        $('.initialVat').html(obj['initialVat']);
        $('.initialDiscount').html(obj['initialDiscount']);
       // $('.payment').val(obj['initialGrandTotal']);
        $('.due').html(obj['initialGrandTotal']);
        $('#restaurant_invoice_discount').val(obj['initialDiscount']);
        if(obj['initialGrandTotal'] > 0 ){
            $('.receiveBtn').attr("disabled", false);
        }else{
            $('.receiveBtn').attr("disabled", true);
        }
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
}





