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
            $('#particularId').val(obj['particularId']);
            $('#quantity').focus().val('');
            $('#price').val(obj['price']);
            $('#unit').html(obj['unit']);
        }
    })
});

var form = $("#purchaseItem").validate({

    rules: {

        "particular": {required: true},
        "price": {required: false},
        "quantity": {required: true},
    },

    messages: {

        "particular":"Enter particular name",
        "price":"Enter purchase price",
        "quantity":"Enter purchase quantity",
    },
    tooltip_options: {
        "particular": {placement:'top',html:true},
        "price": {placement:'top',html:true},
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
                $("#particular").select2().select2("val","");
                $('form#purchaseItem')[0].reset();
                $('#unit').html('Unit');
                $('#quantity').val('1');

            }
        });
    }
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

        }
    })
});


$(document).on("click", ".confirmSubmit", function() {
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('form').submit();
        }
    });

});

