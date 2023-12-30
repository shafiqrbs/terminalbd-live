$("#addStore").click(function(){
    $( "#storeLedger" ).slideToggle( "slow" );
}).toggle( function() {
    $(this).removeClass("purple").addClass("red").html('<i class="icon-remove"></i>');
}, function() {
    $(this).removeClass("red").addClass("purple").html('<i class="icon-user"></i>');
});

$('form#distributionInvoice').on('keypress', '.input', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);
        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {

            case 'particular':
                $('#quantity').focus();
                break;

            case 'quantity':
                $('#addDistribution').click();
                $('#particular').select2('open');
                break;
        }
        return false;
    }
});

var formDistributionInvoice = $("#distributionInvoice").validate({
    rules: {

        "particular": {required: true},
        "quantity": {required: true},
        "salesPrice": {required: true}
    },

    messages: {

        "particular":"Enter particular name",
        "salesPrice":"Enter sales price",
        "quantity":"Enter sales quantity"
    },

    tooltip_options: {
        "particular": {placement:'top',html:true},
        "salesPrice": {placement:'top',html:true},
        "quantity": {placement:'top',html:true}
    },

    submitHandler: function(distributionInvoice) {

        $.ajax({
            url         : $('form#distributionInvoice').attr( 'action' ),
            type        : $('form#distributionInvoice').attr( 'method' ),
            data        : new FormData($('form#distributionInvoice')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                obj = JSON.parse(response);
                $('#invoiceParticulars').html(obj['invoiceParticulars']);
                $('#unit').html('Unit');
               // $("#particular").select2('open').select2("val","");
                $('#particular').select2('open');
                document.getElementById('distributionInvoice').reset();
                returnData(response);
            }
        });
    }
});


$(document).on('change', '.salesPrice,.salesQuantity , .bonusQuantity , .returnQuantity , .damageQuantity , .spoilQuantity, .tloPrice, .srCommission', function() {

    var id = $(this).attr('data-id');
    var price = parseFloat($('#salesPrice-'+id).val());
    var salesQuantity = parseFloat($('#salesQuantity-'+id).val());
    var bonusQuantity = parseFloat($('#bonusQuantity-'+id).val());
    var returnQuantity = parseFloat($('#returnQuantity-'+id).val());
    var damageQuantity = parseFloat($('#damageQuantity-'+id).val());
    var spoilQuantity = parseFloat($('#spoilQuantity-'+id).val());
    var tloPrice = ($('#tloPrice-'+id).val() !="" && $('#tloPrice-'+id).val() !="NaN") ? parseInt($('#tloPrice-'+id).val()) : 0;
    var srCommission = ($('#tloPrice-'+id).val() !="" && $('#tloPrice-'+id).val() !="NaN") ? parseInt($('#srCommission-'+id).val()) : 0;
    var tloMode = $('#tloMode-'+id).val();
    var totalQuantity  = (salesQuantity - returnQuantity - damageQuantity - spoilQuantity);
    var subTotal  = (totalQuantity * price);
    $("#totalQuantity-"+id).html(totalQuantity);

    if(tloMode === 'flat' && tloPrice > 0){
        $(".tloPrice-"+id).html(financial(tloPrice));
    }
    if(tloMode === 'multiply' && tloPrice > 0 ){
        var subTlo = (tloPrice * totalQuantity);
        $(".tloPrice-"+id).html(financial(subTlo));
    }
    if(tloMode === 'percent' && tloPrice > 0){
        var percent = ((price * tloPrice)/100);
        var subTlo = (percent * totalQuantity);
        $(".tloPrice-"+id).html(financial(subTlo));
    }

    if(tloMode === 'flat' && srCommission > 0 ){
        $(".srCommissionTotal-"+id).html(financial(srCommission));
    }
    if(tloMode === 'multiply' && srCommission > 0){
        var subTlo = (srCommission * totalQuantity);
        $(".srCommissionTotal-"+id).html(financial(subTlo));
    }
    if(tloMode === 'percent' && srCommission > 0){
        var percent = ((price * srCommission)/100);
        var subTlo = (percent * totalQuantity);
        $(".srCommissionTotal-"+id).html(financial(subTlo));
    }

    $("#subTotal-"+id).html(financial(subTotal));
    $.ajax({
        url: Routing.generate('business_invoice_distribution_item_update'),
        type: 'POST',
        data:'itemId='+ id +'&salesPrice='+ price +'&salesQuantity='+ salesQuantity +'&bonusQuantity='+ bonusQuantity +'&returnQuantity='+ returnQuantity +'&damageQuantity='+ damageQuantity  +'&spoilQuantity='+ spoilQuantity +'&totalQuantity='+ totalQuantity +'&tloPrice='+ tloPrice +'&tloMode='+ tloMode+'&srCommission='+srCommission,
        success: function(response) {
            returnData(response);
        }
    })
});

function returnData(response)
{
    obj = JSON.parse(response);
    $('.subTotal').html(financial(obj['subTotal']));
    $('.netTotal').html(financial(obj['subTotal']));
    $('.tloPrice').html(financial(obj['tloPrice']));
    $('.srCommission').html(financial(obj['srCommission']));
    $('.salesReturn').html(financial(obj['salesReturn']));
    var due = (obj['subTotal'] - obj['tloPrice']);
    $('#paymentTotal').val(financial(due));
    $('#due').val(financial(due));
    $('.due').html(financial(due));
    $('.payment').html(financial(due));
    $('.discount').html(0);
    $('.salesQnt').html(obj['salesQnt']);
    $('.returnQnt').html(obj['returnQnt']);
    $('.damageQnt').html(obj['damageQnt']);
    $('.spoilQnt').html(obj['spoilQnt']);
    $('.totalQnt').html(obj['totalQnt']);;
    $('.bonusQnt').html(obj['bonusQnt']);
}

$(document).on("click", ".distributionDelete", function(event) {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(el) {
            $.get(url, function( response ) {
                $(event.target).closest('tr').remove();
                returnData(response);
            });
        }
    });
});



var storeForm = $("#storeForm").validate({

    rules: {
        "store": {required: true},
        "amount": {required: true},
    },
    tooltip_options: {
        "store": {placement:'top',html:true},
        "amount": {placement:'top',html:true},
    },
    submitHandler: function(storeForm) {

        $.ajax({
            url         : $('form#storeForm').attr( 'action' ),
            type        : $('form#storeForm').attr( 'method' ),
            data        : new FormData($('form#storeForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                $('#storeLedgerItem').html(response);
                document.getElementById('storeLedgerForm').reset();
            }
        });
    }
});

function salesReturnData(response) {
    obj = JSON.parse(response);
    $('#salesReturnItem').html(obj['invoiceParticulars']);
    $('#subTotal').html(obj['subTotal']);
    $('.salesReturn').html(obj['salesReturn']);
    $('.netTotal').html(obj['netTotal']);
    $('.tloPrice').html(obj['tloPrice']);
}

var salesReturnForm = $("#salesReturnForm").validate({

    rules: {
        "returnItem": {required: true},
        "quantity": {required: true},
        "amount": {required: true},
        "itemProcess": {required: true},
    },
    tooltip_options: {
        "returnItem": {placement:'top',html:true},
        "quantity": {placement:'top',html:true},
        "amount": {placement:'top',html:true},
    },

    submitHandler: function(salesReturnForm) {

        $.ajax({
            url         : $('form#salesReturnForm').attr( 'action' ),
            type        : $('form#salesReturnForm').attr( 'method' ),
            data        : new FormData($('form#salesReturnForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                salesReturnData(response);
                document.getElementById('salesReturnForm').reset();
            }
        });
    }
});

$(document).on("click", ".returnItemDelete", function(event) {

    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(el) {
            $.get(url, function( response ) {
                $(event.target).closest('tr').remove();
                salesReturnData(response);
            });
        }
    });
});


$(document).on('change', '#store', function() {
    var store = $(this).val();
    $.ajax({
        url: Routing.generate('business_invoice_distribution_store_balance',{'id':store}),
        type: 'GET',
        success: function (response) {
            $('#storeOutstanding').html(response);
        }
    })

});

var formLedger = $("#storeLedgerForm").validate({

    rules: {
        "store": {required: true},
        "storeMobile": {required: true},
        "dsm": {required: true},
        "area": {required: true},
    },
    tooltip_options: {
        "store": {placement:'top',html:true},
        "storeMobile": {placement:'top',html:true},
        "dsm": {placement:'top',html:true},
        "area": {placement:'top',html:true},
    },

    submitHandler: function(formLedger) {

        $.ajax({
            url         : $('form#storeLedgerForm').attr( 'action' ),
            type        : $('form#storeLedgerForm').attr( 'method' ),
            data        : new FormData($('form#storeLedgerForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                $('#store').html(response);
                document.getElementById('storeLedgerForm').reset();
            }
        });
    }
});


$(document).on("click", ".ledgerDelete", function(event) {

    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(el) {
            $.get(url, function( data ) {
                $(event.target).closest('tr').remove();
                $('#storeLedgerItem').html(response);
            });
        }
    });
});



