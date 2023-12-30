/**
 * Created by rbs on 5/1/17.
 */
function financial(val) {
    return Number.parseFloat(val).toFixed(2);
}

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




$(document).on('change', '#purchaseItem_stockName', function() {

    var medicine = $('#purchaseItem_stockName').val();
    $.ajax({
        url: Routing.generate('medicine_purchase_particular_search',{'id':medicine}),
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#purchaseItem_expirationStartDate').focus();
            $('#purchaseItem_salesPrice').val(obj['salesPrice']);
            $('#purchaseItem_purchasePrice').val(obj['purchasePrice']);
            $('#unit').html(obj['unit']);
        }
    })

});

$('form#stockItemForm').on('keyup', '#medicineStock_purchasePrice', function (e) {
    var mrp = $('#medicineStock_purchasePrice').val();
    $('#medicineStock_salesPrice').val(mrp);
});

$('#purchaseItem_stockName').on("select2-selecting", function (e) {
    setTimeout(function () {
        $('#purchaseItem_stockName').focus();
    }, 2000)
});


$('form#purchaseItemForm').on('keyup', '#purchaseItem_purchasePrice', function (e) {
    var mrp = $('#purchaseItem_purchasePrice').val();
    $('#purchaseItem_salesPrice').val(mrp);
});


$('form#stockItemForm').on('keypress', '.stockInput', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);
        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {

            case 'medicineStock_purchaseQuantity':
                $('#medicineStock_purchasePrice').focus();
                $("#stockItemCreate").attr("disabled", false);
                break;
            case 'medicineStock_purchasePrice':
                $('#medicineStock_unit').focus();
                break;
            case 'medicineStock_unit':
                $('#stockItemCreate').focus();
                break;

        }
        return false;
    }
});

var formStock = $("#stockItemForm").validate({
    rules: {
        "medicineStock[name]": {required: true},
        "medicineStock[rackNo]": {required: false},
        "medicineStock[unit]": {required: false},
        "medicineStock[purchasePrice]": {required: false},
        "medicineStock[purchaseQuantity]": {required: false}
    },
    messages: {
        "medicineStock[name]":"Enter medicine name",
    },
    tooltip_options: {
        "medicineStock[name]": {placement:'top',html:true},
    },

    submitHandler: function(formStock) {

        $.ajax({
            url         : $('form#stockItemForm').attr( 'action' ),
            type        : $('form#stockItemForm').attr( 'method' ),
            data        : new FormData($('form#stockItemForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                obj = JSON.parse(response);
                if(obj['success'] === 'invalid'){
                    alert('This item already exist in stock item');
                    return false;
                }
                $('#invoiceParticulars').html(obj['invoiceParticulars']);
                $('#subTotal').html(obj['subTotal']);
                $('#vat').val(obj['vat']);
                $('.grandTotal').html(obj['netTotal']);
                $('#paymentTotal').val(obj['netTotal']);
                $('#due').val(obj['due']);
                $('.dueAmount').html(obj['due']);
                $('#discount').html(obj['discount']);
                $('#msg').html(obj['msg']);
                $("#medicineStock_name").select2("val", "");
                $("#medicineId").val();
                $("#stockItemCreate").attr("disabled", false);
                $('#stockItemForm')[0].reset();
                EditableInit();
            }
        });
    }
});

$('#invoiceParticulars').on("click", ".delete", function() {

    var url = $(this).attr("data-url");
    var id = $(this).attr("id");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            $('#remove-'+id).hide();
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('.grandTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
            $('#discount').html(obj['discount']);
            $('#msg').html(obj['msg']);
        }
    })
});


$(document).on('change', '.quantity , .purchasePrice ,.salesPrice', function() {

    var id = $(this).attr('data-id');
    var quantity = parseFloat($('#quantity-'+id).val());
    var salesPrice = parseFloat($('#salesPrice-'+id).val());
    var subTotal  = (quantity * salesPrice);
    $("#subTotal-"+id).html(financial(subTotal));
    $.ajax({
        url: Routing.generate('medicine_prepurchase_item_update'),
        type: 'POST',
        data:'purchaseItemId='+ id +'&quantity='+ quantity +'&salesPrice='+ salesPrice,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#total').html(obj['netTotal']);
            $('#distcount').val(obj['discount']);
        },

    })
});


$(document).on('change', '#medicinepurchase_discountCalculation', function() {

    var discount = parseFloat($('#medicinepurchase_discountCalculation').val());
    var purchase = parseInt($('#purchaseId').val());
    $.ajax({
        url: Routing.generate('medicine_prepurchase_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&purchase='+purchase,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#total').html(obj['netTotal']);
            $('#discount').html(obj['discount']);
        }

    })

});


$(document).on('change', '#medicinepurchase_payment , #medicinepurchase_discount', function() {

    var payment     = parseInt($('#medicinepurchase_payment').val()  != '' ? $('#medicinepurchase_payment').val() : 0 );
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



$('form#purchaseForm').on('keypress', '.inputs', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);

        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {

            case 'medicinepurchase_discountCalculation':
            $('#medicinepurchase_payment').focus();
            break;

            case 'medicinepurchase_payment':
            $('#receiveBtn').focus();
            break;
        }
        return false;
    }
});

$(document).on("click", ".confirmSubmit", function() {
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('form#purchaseForm').submit();
        }
    });

});

function brandMedicineSearch(purchase){

    var url = Routing.generate('medicine_prepurchase_stock_item_search',{'purchase':purchase});
    $(".select2StockMedicinePurchaseItem").select2({

        placeholder: "Search stock medicine name",
        ajax: {
            url: url,
            dataType: 'json',
            delay: 100,
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
            $.ajax(Routing.generate('medicine_stock_name', { stock : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });
        },
        allowClear: true,
        minimumInputLength: 1

    });

    $(document).on("change", "#brandName", function() {
        var brand = $(this).val();
        var purchase = $('#purchaseId').val();
        $.ajax({
            url: Routing.generate('medicine_prepurchase_update_brand',{'id':purchase,'brandName':brand}),
            type: 'GET',
            success: function (response) {
                console.log('done');
            }
        })
    });

}

