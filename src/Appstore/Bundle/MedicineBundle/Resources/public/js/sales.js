/**
 * Created by rbs on 5/1/17.
 */

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

$(".addCustomer").click(function(){
    $( ".customer" ).slideToggle( "slow" );
}).toggle( function() {
    $(this).removeClass("blue").addClass("red").html('<i class="icon-remove"></i>');
}, function() {
    $(this).removeClass("red").addClass("blue").html('<i class="icon-user"></i>');
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

$(document).on('change', '#salesitem_stockName', function() {

    var medicine = $('#salesitem_stockName').val();
    if(medicine === ""){ return false }
    $.ajax({
        url: Routing.generate('medicine_sales_stock_search',{'id':medicine}),
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#salesitem_salesPrice').val(obj['salesPrice']);
            $('#salesitem_quantity').focus();
            $("#addParticular").attr("disabled", false);
        }
    })

});

$('#salesitem_stockName').on("select2-selecting", function (e) {
    setTimeout(function () {
        $('#salesitem_stockName').focus();
    }, 2000)
});


$('form#salesItemForm').on('keypress', '.input', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);
        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {

            case 'salesitem_stockName':
                $('#salesitem_quantity').focus();
                break;

            case 'salesitem_quantity':
                $('#addParticular').click();
                $('#salesitem_stockName').select2('open');
                break;

        }
        return false;
    }
});

var form = $("#salesItemForm").validate({

    rules: {

        "salesitem[stockName]": {required: true},
        "salesitem[salesPrice]": {required: true},
        "salesitem[itemPercent]": {required: false},
        "salesitem[quantity]": {required: false},
    },

    messages: {

        "salesitem[medicineStock]":"Enter medicine name",
        "salesitem[barcode]":"Select barcode",
        "salesitem[salesPrice]":"Enter sales price",
        "salesitem[quantity]":"Enter medicine quantity",
    },
    tooltip_options: {
        "salesitem[medicineStock]": {placement:'top',html:true},
        "salesitem[barcode]": {placement:'top',html:true},
        "salesitem[salesPrice]": {placement:'top',html:true},
        "salesitem[quantity]": {placement:'top',html:true},
    },

    submitHandler: function(form) {

        $.ajax({
            url         : $('form#salesItemForm').attr( 'action' ),
            type        : $('form#salesItemForm').attr( 'method' ),
            data        : new FormData($('form#salesItemForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                obj = JSON.parse(response);
                $('#invoiceParticulars').html(obj['salesItems']);
                $('#subTotal').html(obj['subTotal']);
                $('#vat').val(obj['vat']);
                $('.grandTotal').html(obj['netTotal']);
                $('#paymentTotal').val(obj['netTotal']);
                $('#due').val(obj['due']);
                $('.dueAmount').html(obj['due']);
                $('#msg').html(obj['msg']);
                $("#salesitem_stockName").select2("val", "");
                $('#salesItemForm')[0].reset();
                $('#addParticular').html('<i class="fa fa-shopping-cart"></i> Add').attr("disabled", true);
            }
        });
    }
});

function financial(val) {
    var number =  Number.parseFloat(val).toFixed(2);
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

$(document).on('change', '.quantity ,.salesPrice, .itemPercent', function() {

    var id = $(this).attr('data-id');
    var quantity = parseFloat($('#quantity-'+id).val());
    var salesPrice = parseFloat($('#salesPrice-'+id).val());
    var estimatePrice = parseFloat($('#estimatePrice-'+id).val());
    var itemPercent     = parseFloat($('#itemPercent-'+id).val()  != '' ? $('#itemPercent-'+id).val() : 0 );
    if(itemPercent > 0){
        var amount = (estimatePrice-(estimatePrice * itemPercent/100));
    }else{
        var amount = salesPrice;
    }
    var subTotal  = (quantity * amount);
    $("#subTotal-"+id).html(financial(subTotal));

    $.ajax({
        url: Routing.generate('medicine_sales_item_update'),
        type: 'POST',
        data:'id='+ id +'&quantity='+ quantity+'&salesPrice='+ salesPrice+'&itemPercent='+ itemPercent,
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
            $('.grandTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
            $('#msg').html(obj['msg']);
        }
    })

});

$(document).on('keyup', '#sales_discountCalculation', function() {

    var discountType = $('#sales_discountType').val();
    var discount = parseFloat($('#sales_discountCalculation').val());
    var invoice = $('#invoiceId').val();
    var total =  parseFloat($('#dueAmount').val());
    if( discount >= total ){
        $('#sales_discount').val(0);
        return false;
    }
    $.ajax({
        url: Routing.generate('medicine_sales_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&discountType='+discountType+'&invoice='+invoice,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('.grandTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#sales_discount').val(obj['discount']);
            $('.discount').html(obj['discount']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
        }

    })

});

$(document).on('keyup', '#sales_received , #discount', function() {

    var payment     = parseFloat($('#sales_received').val()  != '' ? $('#sales_received').val() : 0 );
    var discount     = parseFloat($('#discount').val()  != '' ? $('#discount').val() : 0 );
    var due =  parseInt($('#due').val());
    var dueAmount = (due-discount-payment);
    if(dueAmount > 0){
        $('#balance').html('Due Tk.');
        $('.dueAmount').html(dueAmount);
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return Tk.');
        $('.dueAmount').html(balance);
    }
});

$('form#salesForm').on('keypress', '.salesInput', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);
        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {
            case 'sales_transactionMethod':
                $('#sales_salesBy').focus();
                break;
            case 'sales_salesBy':
                $('#sales_received').focus();
                break;
            case 'sales_received':
                $('#receiveBtn').focus();
                break;
        }
        return false;
    }
});

$(document).on("click", "#receiveBtn", function() {
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('form#salesForm').submit();
        }
    });
});

