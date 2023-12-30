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

function financial(val) {
    return Number.parseFloat(val).toFixed(2);
}

$(document).on('change', '.transactionMethod', function() {

    var paymentMethod = $(this).val();

    if( paymentMethod === 2){
        $('#cartMethod').show();
        $('#bkashMethod').hide();
    }else if( paymentMethod === 3){
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
            $('#purchasePrice').val(obj['purchasePrice']);
            $('#unit').html(obj['unit']);
        }
    })
});

$(document).on('keyup', ".amount", function() {
    var sum = 0;
    var id = $(this).attr('data-id');
    var url = $("#amount-"+id).attr('data-action');
    var amount = $("#amount-"+id).val();
    $.get(url,{amount:amount} );
    $(".amount").each(function(){
        sum += + parseFloat($(this).val());
    });
    total = financial(sum);
    $("#valueTotal").html(total);
});

$(document).on('click', '#addParticular', function() {

    var particularId = $('#particularId').val();
    var quantity = $('#quantity').val();
    var price = $('#purchasePrice').val();
    var url = $('#addParticular').attr('data-url');
    if(particularId === ''){
        $("#particular").select2('open');
        return false;
    }
    if(price === ''){
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
            $('#discountAmount').html(obj['discount']);
            $('.grandTotal').html(obj['grandTotal']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
            $('#purchasePrice').val('');
            $("#particular").select2().select2("val","").select2('open');
            $('#price').val('');
            $('#quantity').val('1');
        }
    })
});
$(document).on('change', '#discount', function() {

    var discount = parseInt($('#discount').val());
    var purchaseId = parseInt($('#purchaseId').val());
    $.ajax({
        url: Routing.generate('restaurant_purchase_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&invoice='+ purchaseId,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#discountAmount').html(obj['discount']);
            $('.grandTotal').html(obj['grandTotal']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
        },

    })
});


$(document).on("click", ".confirm", function() {

});

$('#invoiceParticulars').on("click", ".delete", function() {


    var url = $(this).attr("data-url");
    var id = $(this).attr("id");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('#remove-'+id).hide();
            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    obj = JSON.parse(response);
                    $('#invoiceParticulars').html(obj['invoiceParticulars']);
                    $('#subTotal').html(obj['subTotal']);
                    $('#discountAmount').html(obj['discount']);
                    $('.grandTotal').html(obj['grandTotal']);
                    $('#due').val(obj['due']);
                    $('.dueAmount').html(obj['due']);
                }
            })
        }
    });


});

$(document).on('change', '#purchase_payment', function() {

    var payment     = parseInt($('#purchase_payment').val()  != '' ? $('#purchase_payment').val() : 0 );
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
