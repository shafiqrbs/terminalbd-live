/**
 * Created by rbs on 5/1/17.
 */

$(document).on("click", "#submitBtn", function() {
    var url = $(this).attr('data-url');
    var id = $(this).attr('data-id');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('#purchaseReturn').submit();
        }
    });
});

$(".number , .amount, .numeric").inputFilter(function(value) {
    return /^-?\d*[.,]?\d*$/.test(value); });


$(document).on('keyup', '.returnQuantity , .returnPrice', function() {

    var sum = 0;
    var returnSubTotal     = parseFloat($('#returnTotal').val()  != '' ? $('#returnTotal').val() : 0 );
    var id = $(this).attr('data-id');
    var quantity = parseFloat($('#quantity-'+id).val());
    var maxQuantity = parseFloat($('#quantity-'+id).attr('max'));
    var price = parseFloat($('#price-'+id).val());
    if (maxQuantity < quantity) {
        alert("Invalid quantity, less then equal to "+maxQuantity);
        $('#quantity-'+id).val(maxQuantity).focus();
        return false;
    }
    var subTotal  = (quantity * price);
    $("#subTotal-"+id).html(subTotal);
    $(".itemSubtotal").each(function(){
        var total = ($(this).html() === "" || $(this).html() === "NaN") ? 0 : $(this).html();
        sum += +parseFloat(total);
    });
    $('.returnGrandTotal').html(sum);
    $('.returnSubTotal').val(sum);

});

$(document).on('keyup', '#payment', function() {

    var sum = 0;
    var returnSubTotal     = parseFloat($('#returnTotal').val()  != '' ? $('#returnTotal').val() : 0 );
    var payment = parseFloat($('#payment').val()  != '' ? $('#payment').val() : 0 );
    var amount = (returnSubTotal - payment);
    if (returnSubTotal < amount) {
        alert("Invalid amount & adjustment amount");
        $(this).val(0);
        return false;
    }else{
        $('#adjustment').val(amount);
    }
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

$(".select2StockMedicine").select2({

    placeholder: "Search stock medicine name",
    ajax: {
        url: Routing.generate('medicine_stock_search'),
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
    formatResult: function (item) { return item.text}, // omitted for brevity, see the source of this page
    formatSelection: function (item) { return item.text }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var id = $(element).val();
        $.ajax(Routing.generate('medicine_stock_name', { vendor : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1

});

$(document).on('change', '#damage_medicineStock', function() {

    var medicine = $('#damage_medicineStock').val();
    if(medicine === ""){ return false }
    $.ajax({
        url: Routing.generate('medicine_sales_stock_search',{'id':medicine}),
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#medicinePurchaseItem').html(obj['purchaseItems']).focus();
        }
    })

});
