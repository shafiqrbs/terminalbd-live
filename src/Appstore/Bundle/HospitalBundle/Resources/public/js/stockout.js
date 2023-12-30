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

$(document).on("click", ".approve", function() {
    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                location.reload();
            });
        }
    });
});


$(document).on('change', '.particular', function() {

    var id = $(this).val();
    var url = Routing.generate('hms_stockout_particular_search',{'id':id});
    $.get(url, function( response ) {
        obj = JSON.parse(response);
        $('#particularId').val(obj['particularId']);
        $('#quantity').val(obj['quantity']);
        $('#price').val(obj['price']);
    });

});

$(document).on('click', '#addParticular', function() {

    var particularId = $('#particularId').val();
    var quantity = $('#quantity').val();
    var price = $('#price').val();
    var url = $('#addParticular').attr('data-url');
    if(particularId == ''){
        $('.msg-hidden').show();
        $('input[name=particular]').focus();
        $('#msg').html('Please select medicine or accessories name');
        return false;
    }
    if(price == ''){
        $('.msg-hidden').show();
        $('#msg').html('Please enter price');
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
            $('#vat').val(obj['vat']);
            $('.grandTotal').html(obj['grandTotal']);
            $('#paymentTotal').val(obj['grandTotal']);
            $('#due').val(obj['dueAmount']);
            $('.dueAmount').html(obj['dueAmount']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
            $(".particular").select2().select2("val","");
            $('#price').val('');
            $('#quantity').val('1');

        }
    })
});
$(document).on('change', '#discount', function() {

    var discount = parseInt($('#discount').val());
    var purchaseId = parseInt($('#purchaseId').val());
    $.ajax({
        url: Routing.generate('hms_stockout_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&invoice='+ purchaseId,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#vat').val(obj['vat']);
            $('.grandTotal').html(obj['grandTotal']);
            $('#paymentTotal').val(obj['grandTotal']);
            $('#due').val(obj['dueAmount']);
            $('.dueAmount').html(obj['dueAmount']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
        },

    })
});

$('#invoiceParticulars').on("click", ".itemDelete", function() {

    var url = $(this).attr("data-url");
    var id = $(this).attr("id");
    $('#remove-'+id).hide();
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#vat').val(obj['vat']);
            $('.grandTotal').html(obj['grandTotal']);
            $('#due').val(obj['dueAmount']);
            $('.dueAmount').html(obj['dueAmount']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
        }
    })
});

$(document).on('change', '#stockout_payment', function() {

    var payment     = parseInt($('#stockout_payment').val()  != '' ? $('#stockout_payment').val() : 0 );
    var due =  parseInt($('#due').val());
    var dueAmount = (due - payment);
    if(dueAmount > 0){
        $('#balance').html('Due Tk.');
        $('.dueAmount').html(dueAmount);
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return Tk.');
        $('.dueAmount').html(balance);
    }
});

$('form.horizontal-form').on('keypress', 'input', function (e) {

    if (e.which == 13) {
        e.preventDefault();

        switch (this.id) {

            case 'discount':
                $('#paymentAmount').focus();
                break;

            case 'paymentAmount':
                $('#receiveBtn').focus();
                break;
        }
    }
});
