
/**
 * Created by rbs on 2/9/16.
 */



var InventorySalesReturnItem = function(salesReturn) {

    $('#paymentBtn').attr("disabled", false);

    $(document).on('keyup', '.quantity , .price ', function () {

        var rel = $(this).attr('rel');
        var salesReturn = $('#salesReturn-' + rel).val();
        var salesQuantity = $('#salesQuantity-' + rel).val();
        var salesPrice = $('#salesPrice-' + rel).val();
        var quantity = $('#quantity-' + rel).val();
        var price = $('#price-' + rel).val() != '' ? $('#price-' + rel).val() : '0';
        if (quantity == 0 || parseInt(quantity) > parseInt(salesQuantity)) {
            $('#quantity-' + rel).val('');
            $('#quantity-' + rel).focus();
            $("#returnSubTotal-" + rel).html('');
            $("#returnbtn-" + rel) .attr("disabled", true);
            return false;
        }
        if (price == 0 || parseInt(price) > parseInt(salesPrice)) {
            $("#price-" +rel).val('');
            $("#price-" +rel).focus();
            $("#returnSubTotal-" + rel).html('');
            $("#returnbtn-" + rel) .attr("disabled", true);
            return false;
        }
        var subTotal = ( parseInt(quantity) * parseInt(price) );
        $("#returnSubTotal-" + rel).html(subTotal);
        $("#returnbtn-" + rel) .attr("disabled", false);
        $("#cancelbtn-" + rel) .attr("disabled", false);


    })

    $(document).on('click', '.returnItem', function () {

        var item = $(this).attr('rel');
        var quantity = $('#quantity-' + item).val();
        var price = $('#price-' + item).val();
        $.ajax({
            url: Routing.generate('inventory_salesreturn_item'),
            type: 'POST',
            data: 'salesReturn=' + salesReturn + '&item=' + item + '&quantity=' + quantity + '&price=' + price,
            success: function (response) {
                obj = JSON.parse(response);
                if ('success' == obj['success']) {
                    $('#paymentBtn').attr("disabled", false);
                    $("#returnbtn-" + item).hide();
                    $("#message").html(obj['message']);
                    $("#totalAmount").html(obj['totalAmount']);
                    $('#action-' + item).append('<a  href="javascript:" disabled="disabled" class="btn purple mini" ><i class="icon-check"></i>&nbsp;Returned</a>');

                }else{

                    $('#quantity-' + item).val('');
                    $('#price-' + item).val('');
                    $("#returnSubTotal-" + item).html('');
                    $("#returnbtn-" + item) .attr("disabled", true);
                    $("#message").html(obj['message']);

                }
            },
        })
    })

    $(document).on("click", ".delete", function () {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                if ('success' == obj['success']) {
                    location.reload();
                }
            }
        })
    });

};

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

