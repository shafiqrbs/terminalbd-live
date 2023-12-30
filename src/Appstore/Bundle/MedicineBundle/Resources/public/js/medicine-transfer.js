
$(document).on('change', '#discountCalculation , #discountType', function() {

    var discountType = $('#discountType').val();
    var discount = parseFloat($('#discountCalculation').val());
    var purchase = parseInt($('#purchaseId').val());
    $.ajax({
        url: Routing.generate('medicine_transfer_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&discountType='+discountType+'&purchase='+purchase,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#total').html(obj['total']);
            $('#discount').html(obj['discount']);
            $('.dueAmount').html(obj['total']);
        }
    })

});


$(document).on("click", "#discountCalculation", function() {
   $(this).val('');
});
$(document).on("click", "#payment", function() {
   $(this).val('');
});

$(document).on("click", ".approve", function() {
    var url = $(this).attr('data-url');
    var id = $(this).attr('data-id');
    var payment = $('#payment').val();
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url,{'payment':payment}, function( data ) {
                if(data ==='success'){
                    window.location.replace("/medicine/medicine-transfer/"+id+"/show");
                }
            });
        }
    });
});


$(document).on('change', '#payment , #discount', function() {
    var payment     = parseInt($('#payment').val()  != '' ? $('#payment').val() : 0 );
    var due =  parseInt($('#paymentTotal').val());
    var dueAmount = (due-payment);
    if(dueAmount > 0){
        $('#balance').html('Due Tk.');
        $('.dueAmount').html(dueAmount);
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return Tk.');
        $('.dueAmount').html(balance);
    }

});