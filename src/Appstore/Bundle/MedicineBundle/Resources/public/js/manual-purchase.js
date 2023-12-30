$('form#purchaseItemManualForm').on('keyup', '#purchaseItem_quantity', function (e) {

    var sp =  parseFloat($('#stockSalesPrice').val());
    var pp =  parseFloat($('#stockPurchasePrice').val());
    var quantity =  parseFloat($("#purchaseItem_quantity").val());
    var percent =  parseFloat($("#purchaseItem_itemPercent").val());
    if(quantity !== "NaN" && quantity > 0){
        $('#purchaseItem_salesPrice').val(quantity * sp);
        if(percent !== "NaN" && percent > 0){
            var totalPP = parseFloat(quantity * sp);
            var price = (totalPP - ((totalPP * percent)/100));
            $('#purchaseItem_purchasePrice').val(price);
        }else{
            $('#purchaseItem_purchasePrice').val(quantity * pp);
        }
    }else{
        $('#purchaseItem_salesPrice').val(sp);
        $('#purchaseItem_purchasePrice').val(pp);
    }

});

$('form#purchaseItemManualForm').on('change', '#purchaseItem_itemPercent, #purchaseItem_salesPrice', function (e) {

    var sp =  parseFloat($('#purchaseItem_salesPrice').val());
    var percent =  parseFloat($(this).val());
    var price = (sp - ((sp*percent)/100));
    $('#purchaseItem_purchasePrice').val(price);
});

var form = $("#purchaseItemManualForm").validate({
    rules: {
        "purchaseItem[stockName]": {required: true},
        "purchaseItem[salesPrice]": {required: true},
        "purchaseItem[quantity]": {required: false},
        "purchaseItem[itemPercent]": {required: false},
        "purchaseItem[expirationEndDate]": {required: false},
        "purchaseItem[bonusQuantity]": {required: false}
    },

    messages: {
        "purchaseItem[stockName]":"Enter medicine/item name",
        "purchaseItem[salesPrice]":"Enter sales price",
        "purchaseItem[quantity]":"Enter medicine quantity"
    },
    tooltip_options: {
        "purchaseItem[stockName]": {placement:'top',html:true},
        "purchaseItem[salesPrice]": {placement:'top',html:true},
        "purchaseItem[quantity]": {placement:'top',html:true}
    },

    submitHandler: function(form) {
        $.ajax({
            url         : $('form#purchaseItemManualForm').attr( 'action' ),
            type        : $('form#purchaseItemManualForm').attr( 'method' ),
            data        : new FormData($('form#purchaseItemManualForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                obj = JSON.parse(response);
                $('#invoiceParticulars').html(obj['invoiceParticulars']);
                $('#subTotal').html(obj['subTotal']);
                $('#vat').val(obj['vat']);
                $('.grandTotal').html(obj['netTotal']);
                $('#paymentTotal').val(obj['subTotal']);
                $('#due').val(obj['due']);
                $('.dueAmount').html(obj['due']);
                $('#discount').html(obj['discount']);
                $('#msg').html(obj['msg']);
                $("#purchaseItem_stockName").select2("val", "");
                $('#purchaseItemManualForm')[0].reset();
                $('#addPurchaseItem').html('<i class="icon-save"></i> Add').attr("disabled", false);
                $('#opening-box').hide();
                $(".editableText").editable();
                $(".expirationDate").editable();
            }
        });
    }
});

$(document).on('change', '.manualQuantity ,.manualSalesPrice,.manualPurchasePrice,.manualBonusQuantity', function() {

    var id = $(this).attr('data-id');
    var quantity = parseFloat($('#quantity-'+id).val());
    var salesQuantity = parseFloat($('#salesQuantity-'+id).val());
    var salesPrice = parseFloat($('#salesPrice-'+id).val());
    var purchasePrice = parseFloat($('#purchasePrice-'+id).val());
    var bonusQuantity = parseFloat($('#bonusQuantity-'+id).val());
    if(salesQuantity > quantity){
        $('#quantity-'+id).val($('purchaseQuantity-'+id).val());
        alert("Purchase quantity must be more then sales quantity.");
        return false;
    }
    var salesSubTotal  = (quantity * salesPrice);
    var purchaseSubTotal  = (quantity * purchasePrice);
    $("#salesSubTotal-"+id).html(salesSubTotal);
    $("#purchaseSubTotal-"+id).html(purchaseSubTotal);
    $.ajax({
        url: Routing.generate('medicine_purchase_manual_item_update'),
        type: 'POST',
        data:'purchaseItemId='+ id +'&quantity='+ quantity+'&salesPrice='+ salesPrice+'&purchasePrice='+ purchasePrice +'&bonusQuantity='+ bonusQuantity,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#vat').val(obj['vat']);
            $('.grandTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['subTotal']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
            $('#discount').html(obj['discount']);
        },

    })
});
