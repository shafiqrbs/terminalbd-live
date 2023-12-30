
$(document).on('change', '#discountCalculation , #discountType', function() {

    var discountType = $('#discountType').val();
    var discount = parseFloat($('#discountCalculation').val());
    var purchase = parseInt($('#purchaseId').val());
    $.ajax({
        url: Routing.generate('medicine_purchase_return_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&discountType='+discountType+'&purchase='+purchase,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#total').html(obj['total']);
            $('#discount').html(obj['discount']);

        }

    })

});

$(document).on("click", ".approve", function() {
    var url = $(this).attr('data-url');
    var id = $(this).attr('data-id');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                if(data ==='success'){
                    window.location.replace("/medicine/purchase-return/"+id+"/show");
                }
            });
        }
    });
});