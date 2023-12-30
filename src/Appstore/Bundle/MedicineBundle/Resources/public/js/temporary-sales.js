/**
 * Created by rbs on 5/1/17.
 */

$(".number , .amount, .numeric").inputFilter(function(value) {
    return /^-?\d*[.,]?\d*$/.test(value); });

$('.amount').change(function(){
    this.value = parseFloat(this.value).toFixed(2);
});

function financial(val) {
    var number =  Number.parseFloat(val).toFixed(2);
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}


$(document).ready(function(){

    load_data();
    function load_data(query)
    {
        $.ajax({
            url: Routing.generate('medicine_sales_temporary_search'),
            method:"POST",
            data:{query:query},
            success:function(data)
            {
                $('#result').html(data);
                searchScroll();
            }
        });
    }
    $('#search').keyup(function(){
        var search = $(this).val();
        if(search !== '')
        {
            load_data(search);
        }
        else
        {
            load_data();
        }
    });
});

function searchScroll(){
    $('.search-scroll').slimScroll({
        height: '350px'
    });
}

$(document).on("click", ".inlineAddItem", function() {
    var id = $(this).attr('data-id');
    if(id === ''){
        return false;
    }
    $.ajax(Routing.generate('medicine_sales_generic_stock', { id : id}), {
        type: 'GET',
    }).done(function (response) {
        obj = JSON.parse(response);
        $("#addTemporaryItem").attr("disabled", true);
        $('#invoiceParticulars').html(obj['salesItems']);
        $('#subTotal').html(obj['subTotal']);
        $('#grandTotal').html(obj['initialGrandTotal']);
        $('.discount').html(obj['initialDiscount']);
        $('.dueAmount').html(obj['initialGrandTotal']);
        $('#salesSubTotal').val(obj['subTotal']);
        $('#salesNetTotal').val(obj['initialGrandTotal']);
        $('#profit').html(obj['profit']);
        $('#salesTemporary_discount').val(obj['initialDiscount']);
        $('#salesTemporary_due').val(obj['initialGrandTotal']);
        $('.table-responsive').hide();
        $('#search').focus().val('');
        $('.editableText').editable();
    });
});

$('#salesTemporaryItem_stockName').focus().val('');

$(document).on('click', '.search-clear', function() {
    $('.table-responsive').hide();
    $('#search').focus().val('');
});
$(document).on('click', '.addAjaxItem', function() {

    var item = $(this).attr('data-id');
    var url = $(this).attr('data-action');
    var price = $('#salesPrice-'+item).val();
    var quantity = $('#quantity-'+item).val();
    var percent = $('#itemPercent-'+item).val();
    if ($('#isShort-'+item).is(":checked"))
    {
        var isShort = 1;
    } else {
        var isShort = 0;
    }
    $.ajax({
        url:url,
        type:'POST',
        data:'price='+price+'&quantity='+quantity+"&itemPercent="+percent+"&isShort="+isShort,
        success: function (response) {
            obj = JSON.parse(response);
            $("#addTemporaryItem").attr("disabled", true);
            $('#invoiceParticulars').html(obj['salesItems']);
            $('#subTotal').html(obj['subTotal']);
            $('#grandTotal').html(obj['initialGrandTotal']);
            $('.discount').html(obj['initialDiscount']);
            $('.dueAmount').html(obj['initialGrandTotal']);
            $('#salesSubTotal').val(obj['subTotal']);
            $('#salesNetTotal').val(obj['initialGrandTotal']);
            $('#profit').html(obj['profit']);
            $('#salesTemporary_discount').val(obj['initialDiscount']);
            $('#salesTemporary_due').val(obj['initialGrandTotal']);
            $('.table-responsive').hide();
            $('#search').focus().val('');
            $('#isShort').prop('checked',false).removeAttr('checked');
            $('.editableText').EditableInit();
        }
    });

});



$(document).on('click', '#temporarySales', function() {

    $('.dialogModal_header').html('Sales Information');
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: Routing.generate('medicine_sales_temporary_new'),
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                    jqueryTemporaryLoad();
                    $('#salesTemporaryItem_stockName').select2('open');
                    $('#salesTemporary_received').val('');
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});

$(document).on('change', '#salesitem_stockName', function() {

    var medicine = $('#salesitem_stockName').val();
    if(medicine === ""){ return false }
    $.ajax({
        url: Routing.generate('medicine_sales_stock_search',{'id':medicine}),
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#salesitem_barcode').html(obj['purchaseItems']).focus();
            $('#salesitem_salesPrice').val(obj['salesPrice']);
        }
    })

});


$(document).on("click", ".instantPopup", function() {

    var url = $(this).attr('data-url');
    $.ajax({
        url : url,
        beforeSend: function(){
            $('.loader-double').fadeIn(1000).addClass('is-active');
        },
        complete: function(){
            $('.loader-double').fadeIn(1000).removeClass('is-active');
        },
        success:  function (data) {
            $("#instantPurchaseLoad").html(data).show();
            $('#instantPurchasePopup').removeClass("instantPopup").addClass("removePopup");
            jqueryInstantTemporaryLoad();
        }
    });
});
$(document).on("click", ".removePopup", function() {
    $("#instantPurchaseLoad").slideToggle();
    $('#instantPurchasePopup').removeClass("removePopup").addClass("instantPopup");
});

$(document).on("change", "#barcode", function() {
    var barcode = $('#barcode').val();
    if(barcode === ''){
        $('#wrongBarcode').html('<strong>Error!: </strong>Invalid barcode, Please try again.');
        return false;
    }
    url = Routing.generate('medicine_sales_barcode_search');
    $.get(url, {barcode: barcode} , function(response){
        obj = JSON.parse(response);
        $("#addTemporaryItem").attr("disabled", true);
        $('#invoiceParticulars').html(obj['salesItems']);
        $('#subTotal').html(obj['subTotal']);
        $('#grandTotal').html(obj['initialGrandTotal']);
        $('.discount').html(obj['initialDiscount']);
        $('.dueAmount').html(obj['initialGrandTotal']);
        $('#salesSubTotal').val(obj['subTotal']);
        $('#salesNetTotal').val(obj['initialGrandTotal']);
        $('#profit').html(obj['profit']);
        $('#salesTemporary_discount').val(obj['initialDiscount']);
        $('#salesTemporary_due').val(obj['initialGrandTotal']);
        $('#barcode').focus().val('');
    });
});

function jqueryTemporaryLoad() {

    $(document).on("click", ".profitBtn", function() {
        $("#profit").slideToggle(100);
    });

    $('#salesTemporary_received').click(function() {
        $('#salesTemporary_received').attr('value', '');
    });

    $(document).on("change", "#genericStock", function() {
        var id = $(this).val();
        if(id === ''){
            return false;
        }
        $.ajax(Routing.generate('medicine_sales_generic_stock', { id : id}), {
            type: 'GET',
        }).done(function (response) {
            obj = JSON.parse(response);
            $("#addTemporaryItem").attr("disabled", true);
            $('#invoiceParticulars').html(obj['salesItems']);
            $('#subTotal').html(obj['subTotal']);
            $('#grandTotal').html(obj['initialGrandTotal']);
            $('.discount').html(obj['initialDiscount']);
            $('.dueAmount').html(obj['initialGrandTotal']);
            $('#salesSubTotal').val(obj['subTotal']);
            $('#salesNetTotal').val(obj['initialGrandTotal']);
            $('#profit').html(obj['profit']);
            $('#salesTemporary_discount').val(obj['initialDiscount']);
            $('#salesTemporary_due').val(obj['initialGrandTotal']);
            $('#generic-stock-hide').hide();
            $('#genericStock').val('').trigger('change').select2('open');
            $('.editableText').editable();
        });
    });

    $(".selectStock2Generic").select2({

        placeholder: "Search generic by stock medicine",
        ajax: {
            url: Routing.generate('medicine_generic_stock_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    pram: params,
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
        formatResult: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        formatSelection: function (item) {
            return item.text
        },
        allowClear: true,
        minimumInputLength:2
    });

    $(".addTemporaryCustomer").click(function(){
        $( ".customer" ).slideToggle( "slow" );
    }).toggle( function() {
        $(this).removeClass("blue").addClass("red").html('<i class="icon-remove"></i>');
    }, function() {
        $(this).removeClass("red").addClass("blue").html('<i class="icon-user"></i>');
    });

    $('form#salesTemporaryItemForm').on('keypress', '.input', function (e) {

        if (e.which === 13) {
            var inputs = $(this).parents("form").eq(0).find("input,select");
            var idx = inputs.index(this);
            if (idx === inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus(); //  handles submit buttons
            }
            switch (this.id) {
                case 'salesTemporaryItem_stockName':
                    $('#salesTemporaryItem_quantity').focus();
                    break;

                case 'salesTemporaryItem_quantity':
                    $('#salesTemporaryItem_itemPercent').focus();
                    break;

                case 'salesTemporaryItem_itemPercent':
                    $('#salesTemporaryItem_salesPrice').focus();
                    break;

                case 'salesTemporaryItem_salesPrice':
                    $('#addTemporaryItem').click();
                    $('#salesTemporaryItem_stockName').select2('open');
                    break;
            }
            return false;
        }
    });

    $('form#medicineStock').on('keypress', '.stockInput', function (e) {

        if (e.which === 13) {
            var inputs = $(this).parents("form").eq(0).find("input,select");
            var idx = inputs.index(this);

            if (idx === inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus(); //  handles submit buttons
            }
            switch (this.id) {

                case 'medicineStock_name':
                    $('#medicineStock_purchaseQuantity').focus();
                    break;

                case 'medicineStock_purchaseQuantity':
                    var qnt = $('#medicineStock_purchaseQuantity').val();
                    if(qnt == "NaN" || qnt =="" ){
                        $('#medicineStock_purchaseQuantity').focus();
                    }else{
                        $('#medicineStock_purchasePrice').focus();
                    }
                    break;
                case 'medicineStock_purchasePrice':
                    var price = $('#medicineStock_purchasePrice').val();
                    if(price == "NaN" || price =="" ){
                        $('#medicineStock_purchasePrice').focus();
                    }else {
                        $('#stockItemCreate').click();
                        $('#medicineStock_name').focus();
                    }
                    break;
                case 'medicineStock_unit':
                    $('#stockItemCreate').click();
                    $('#medicineStock_name').focus();
                    break;

            }
            return false;
        }
    });

    $(document).on('change', '#salesTemporaryItem_stockName', function() {

        var medicine = $('#salesTemporaryItem_stockName').val();
        if(medicine === ""){ return false }
        $.ajax({
            url: Routing.generate('medicine_sales_stock_search',{'id':medicine}),
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                $('#salesTemporaryItem_quantity').focus();
                $('#salesTemporaryItem_purchaseItem').html(obj['purchaseItems']).focus();
                $('#addTemporaryItem').html('<i class="fa fa-shopping-cart"></i> Add').attr("disabled", false);
                $('#salesTemporaryItem_salesPrice').val(obj['salesPrice']);
                $('#genericItems').html(obj['genericItems']);
                $('.editableText').editable();
            }
        })

    });

    var formTemporary = $("#salesTemporaryItemForm").validate({

        rules: {

            "salesTemporaryItem[stockName]": {required: true},
            "salesTemporaryItem[purchaseItem]": {required: false},
            "salesTemporaryItem[itemPercent]": {required: false},
            "salesTemporaryItem[salesPrice]": {required: false},
            "salesTemporaryItem[quantity]": {required: false},
        },

        messages: {

            "salesTemporaryItem[medicineStock]":"Enter medicine name",
            "salesTemporaryItem[salesPrice]":"Enter sales price",
            "salesTemporaryItem[quantity]":"Enter medicine quantity",
        },
        tooltip_options: {
            "salesTemporaryItem[medicineStock]": {placement:'top',html:true},
            "salesTemporaryItem[barcode]": {placement:'top',html:true},
            "salesTemporaryItem[salesPrice]": {placement:'top',html:true},
            "salesTemporaryItem[quantity]": {placement:'top',html:true},
        },

        submitHandler: function(formTemporary) {

            $.ajax({
                url         : $('form#salesTemporaryItemForm').attr( 'action' ),
                type        : $('form#salesTemporaryItemForm').attr( 'method' ),
                data        : new FormData($('form#salesTemporaryItemForm')[0]),
                processData : false,
                contentType : false,
                success: function(response){
                    obj = JSON.parse(response);
                    $("#addTemporaryItem").attr("disabled", true);
                    $('#invoiceParticulars').html(obj['salesItems']);
                    $('#subTotal').html(obj['subTotal']);
                    $('#grandTotal').html(obj['initialGrandTotal']);
                    $('.discount').html(obj['initialDiscount']);
                    $('.dueAmount').html(obj['initialGrandTotal']);
                    $('#salesSubTotal').val(obj['subTotal']);
                    $('#salesNetTotal').val(obj['initialGrandTotal']);
                    $('#profit').html(obj['profit']);
                    $('#salesTemporary_discount').val(obj['initialDiscount']);
                    $('#salesTemporary_due').val(obj['initialGrandTotal']);
                    $('#salesTemporaryItem_stockName').val('').trigger('change').select2('open');
                    if( $('#isShort').is(':checked') ) {
                        $("#isShort").prop("checked", false);
                        $('#uniform-isShort span').removeClass('checked');
                    }
                    $('#salesTemporaryItemForm')[0].reset();
                    $('#addTemporaryItem').html('<i class="fa fa-shopping-cart"></i> Add').attr("disabled", true);
                    $('.salesBtn').prop("disabled", false);
                    $('#printWithoutDiscount, #process').prop('checked', false);
                    $('.editableText').editable();
                }
            });
        }
    });

    var formStock = $("#medicineStock").validate({
        rules: {
            "medicineStock[name]": {required: true},
            "medicineStock[purchaseQuantity]": {required: true},
            "medicineStock[unit]": {required: false},
            "medicineStock[salesPrice]": {required: true},
        },
        messages: {
            "medicineStock[name]": "Enter medicine name",
            "medicineStock[unit]": "Enter medicine unit",
            "medicineStock[salesPrice]": "Enter mrp price",
            "medicineStock[purchaseQuantity]": "Enter purchase quantity",
        },
        tooltip_options: {
            "medicineStock[name]": {placement: 'top', html: true},
            "medicineStock[unit]": {placement: 'top', html: true},
            "medicineStock[salesPrice]": {placement: 'top', html: true},
            "medicineStock[purchaseQuantity]": {placement: 'top', html: true},
        },

        submitHandler: function (formStock) {
            $.ajax({
                url: $('form#medicineStock').attr('action'),
                type: $('form#medicineStock').attr('method'),
                data: new FormData($('form#medicineStock')[0]),
                processData: false,
                contentType: false,
                success: function (response) {
                    obj = JSON.parse(response);
                    $('#invoiceParticulars').html(obj['salesItems']);
                    $("#medicineStock_name").select2("val", "");
                    $("#medicineStock_rackNo").select2("val", "");
                    $("#medicineStock_unit").select2("val", "");
                    $("#medicineId").val();
                    $('#medicineStock')[0].reset();
                    $('#opening-box').hide();
                    $('.editableText').editable();
                }
            });
        }
    });

    $(document).on("change", ".select2Customer , .select2TemporaryCustomer", function() {
        var customer = $(this).val();
        $.get( Routing.generate('domain_customer_sales_ledger'),{ customer:customer})
            .done(function(data){ $('#outstanding').html(data); });
    });

    $(document).on( "click", "#stockShow", function(e){
        $('#hide').slideToggle(2000);
        $("i", this).toggleClass("fa fa-angle-double-up fa fa-angle-double-down");
        $('#medicineStock_name').focus();
    });

    $('form#medicineStock').on('keypress', '.stockInput', function (e) {

        if (e.which === 13) {
            var inputs = $(this).parents("form").eq(0).find("input,select");
            var idx = inputs.index(this);

            if (idx == inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus(); //  handles submit buttons
            }
            switch (this.id) {

                case 'medicineStock_name':
                    $('#medicineStock_purchaseQuantity').focus();
                    break;

                case 'medicineStock_purchaseQuantity':
                    var qnt = $('#medicineStock_purchaseQuantity').val();
                    if(qnt == "NaN" || qnt =="" ){
                        $('#medicineStock_purchaseQuantity').focus();
                    }else{
                        $('#medicineStock_purchasePrice').focus();
                    }
                    break;
                case 'medicineStock_purchasePrice':
                    var price = $('#medicineStock_purchasePrice').val();
                    if(price == "NaN" || price =="" ){
                        $('#medicineStock_purchasePrice').focus();
                    }else {
                        $('#stockItemCreate').click();
                        $('#medicineStock_name').focus();
                    }
                    break;
                case 'medicineStock_unit':
                    $('#stockItemCreate').click();
                    $('#medicineStock_name').focus();
                    break;

            }
            return false;
        }
    });


    $(document).on('change', '.quantity , .salesPrice, .itemPercent', function() {

        var id = $(this).attr('data-id');
        var quantity = parseFloat($('#quantity-'+id).val());
        var price = parseFloat($('#salesPrice-'+id).val());
        var estimatePrice = parseFloat($('#estimatePrice-'+id).val());
        var itemPercent     = parseFloat($('#itemPercent-'+id).val()  != '' ? $('#itemPercent-'+id).val() : 0 );

        var amount = (estimatePrice-(estimatePrice*itemPercent/100));
        var subTotal  = (quantity * amount);

        $("#subTotal-"+id).html(financial(subTotal));

        $.ajax({
            url: Routing.generate('medicine_sales_temporary_item_update'),
            type: 'POST',
            data:'salesItemId='+ id +'&quantity='+ quantity +'&salesPrice='+price+'&itemPercent='+itemPercent,
            success: function(response) {
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('#invoiceParticulars').html(obj['salesItems']);
                $('#grandTotal').html(obj['initialGrandTotal']);
                $('.discount').html(obj['initialDiscount']);
                $('.dueAmount').html(obj['initialGrandTotal']);
                $('#salesSubTotal').val(obj['subTotal']);
                $('#salesNetTotal').val(obj['initialGrandTotal']);
                $('#profit').html(obj['profit']);
                $('#salesTemporary_discount').val(obj['initialDiscount']);
                $('#salesTemporary_due').val(obj['initialGrandTotal']);
                $('.editableText').editable();
            },

        })
    });

    $(document).on('click', '.itemUpdate', function() {

        var id = $(this).attr('data-id');
        var quantity = parseFloat($('#quantity-'+id).val());
        var price = parseFloat($('#salesPrice-'+id).val());
        var subTotal  = (quantity * price);
        $("#subTotal-"+id).html(subTotal);
        $.ajax({
            url: Routing.generate('medicine_sales_temporary_item_update'),
            type: 'POST',
            data:'salesItemId='+ id +'&quantity='+ quantity +'&salesPrice='+ price,
            success: function(response) {
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('#invoiceParticulars').html(obj['salesItems']);
                $('#grandTotal').html(obj['initialGrandTotal']);
                $('.discount').html(obj['initialDiscount']);
                $('.dueAmount').html(obj['initialGrandTotal']);
                $('#salesSubTotal').val(obj['subTotal']);
                $('#salesNetTotal').val(obj['initialGrandTotal']);
                $('#profit').html(obj['profit']);
                $('#salesTemporary_discount').val(obj['initialDiscount']);
                $('#salesTemporary_due').val(obj['initialGrandTotal']);
                $('.editableText').editable();
            },

        })
    });

    $(document).on('click', '.genericStockItem', function() {

        var id = $(this).attr('data-id');
        var quantity = parseFloat($('#quantity-'+id).val());
        var price = parseFloat($('#salesPrice-'+id).val());
        var itemPercent = parseFloat($('#itemPercent-'+id).val());
        var url =$(this).attr('data-action');
        var subTotal  = (quantity * price);
        $("#subTotal-"+id).html(subTotal);
        $.ajax({
            url: url,
            type: 'POST',
            data:'quantity='+ quantity +'&salesPrice='+ price+'&itemPercent='+ itemPercent,
            success: function(response) {
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('#invoiceParticulars').html(obj['salesItems']);
                $('#grandTotal').html(obj['initialGrandTotal']);
                $('.discount').html(obj['initialDiscount']);
                $('.dueAmount').html(obj['initialGrandTotal']);
                $('#salesSubTotal').val(obj['subTotal']);
                $('#salesNetTotal').val(obj['initialGrandTotal']);
                $('#profit').html(obj['profit']);
                $('#salesTemporary_discount').val(obj['initialDiscount']);
                $('#salesTemporary_due').val(obj['initialGrandTotal']);
                $('.editableText').editable();
            },

        })
    });


    $(document).on('change', '#salesTemporary_discountCalculation , #salesTemporary_discountType', function() {

        var discountType = $('#salesTemporary_discountType').val();
        var discount = parseInt($('#salesTemporary_discountCalculation').val());
        $.ajax({
            url: Routing.generate('medicine_sales_temporary_discount_update'),
            type: 'POST',
            data:'discount=' + discount+'&discountType='+discountType,
            success: function(response) {
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('#grandTotal').html(obj['initialGrandTotal']);
                $('.discount').html(obj['initialDiscount']);
                $('.dueAmount').html(obj['initialGrandTotal']);
                $('#salesSubTotal').val(obj['subTotal']);
                $('#salesNetTotal').val(obj['initialGrandTotal']);
                $('#profit').html(obj['profit']);
                $('#salesTemporary_discount').val(obj['initialDiscount']);
                $('#salesTemporary_due').val(obj['initialGrandTotal']);
                if(obj['initialDiscount'] > obj['subTotal']){
                    $('.salesBtn').prop("disabled", true);
                }
            }

        })

    });

    $('#invoiceParticulars').on("click", ".temporaryDelete", function() {

        var url = $(this).attr("data-url");
        var id = $(this).attr("id");
        $('#remove-'+id).hide();
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('#grandTotal').html(obj['initialGrandTotal']);
                $('.discount').html(obj['initialDiscount']);
                $('.dueAmount').html(obj['initialGrandTotal']);
                $('#salesSubTotal').val(obj['subTotal']);
                $('#salesNetTotal').val(obj['initialGrandTotal']);
                $('#profit').html(obj['profit']);
                $('#salesTemporary_discount').val(obj['initialDiscount']);
                $('#salesTemporary_due').val(obj['initialGrandTotal']);
                if(obj['subTotal'] === 0){
                    $('.salesBtn').prop("disabled", true);
                }
            }
        })
    });


    $(document).on('keyup', '#salesTemporary_received', function() {

        var payment     = parseInt($('#salesTemporary_received').val()  != '' ? $('#salesTemporary_received').val() : 0 );
        var discount     = parseInt($('#salesTemporary_discount').val()  != '' ? $('#salesTemporary_discount').val() : 0 );
        var netTotal =  parseInt($('#salesNetTotal').val());
        var dueAmount = (netTotal-payment);
        if(dueAmount > 0){
            $('#balance').html('Due Tk.');
            $('.dueAmount').html(dueAmount);
            $('#salesTemporary_due').val(dueAmount);
        }else{
            var balance =  payment - netTotal ;
            $('#balance').html('Return Tk.');
            $('.dueAmount').html(balance);
            $('#salesTemporary_due').val(0);
        }
    });

    $('form#salesTemporaryForm').on('keypress', '.salesInput', function (e) {

        if (e.which === 13) {
            switch (this.id) {
                case 'salesTemporary_discountCalculation':
                    $('#salesTemporary_received').focus();
                    break;
                case 'salesTemporary_received':
                    $('#receiveBtn').focus();
                    break;
            }
            return false;
        }
    });

    $(document).on("click", "#receiveBtn", function() {

        $('#buttonType').val('receiveBtn');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $('.salesBtn').prop("disabled", true);
                $.ajax({
                    url         : $('form#salesTemporaryForm').attr( 'action' ),
                    type        : $('form#salesTemporaryForm').attr( 'method' ),
                    data        : new FormData($('form#salesTemporaryForm')[0]),
                    processData : false,
                    contentType : false,
                    success: function(response){
                        $('#salesTemporaryForm')[0].reset();
                        $('#invoiceParticulars').html('');
                        $('#subTotal').html('');
                        $('#grandTotal').html('');
                        $('.discount').html('');
                        $('.dueAmount').html('');
                        $('#profit').html('');
                        $('#salesNetTotal').val('');
                        $('#salesSubTotal').val('');
                        $('#salesTemporary_discount').val('');
                        $('#salesTemporary_due').val('');
                        $(".select2TemporaryCustomer").select2("val", "");
                        $(".customer").hide();
                        $('#cartMethod , #bkashMethod').css("display","none");
                        if(response === "failed" ){
                             setTimeout(pageReload, 3000);
                        }
                    }
                });
            }
        });

    });

    $(document).on("click", "#regularPrint", function() {

        $('#buttonType').val('regularBtn');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $('.salesBtn').prop("disabled", true);
                $.ajax({
                    url         : $('form#salesTemporaryForm').attr( 'action' ),
                    type        : $('form#salesTemporaryForm').attr( 'method' ),
                    data        : new FormData($('form#salesTemporaryForm')[0]),
                    processData : false,
                    contentType : false,
                    success: function(response){
                        $('#salesTemporaryForm')[0].reset();
                        $('#invoiceParticulars').html('');
                        $('#subTotal').html('');
                        $('#grandTotal').html('');
                        $('.discount').html('');
                        $('.dueAmount').html('');
                        $('#profit').html('');
                        $('#salesNetTotal').val('');
                        $('#salesSubTotal').val('');
                        $('#salesTemporary_discount').val('');
                        $('#salesTemporary_due').val('');
                        $(".select2TemporaryCustomer").select2("val", "");
                        $(".customer").hide();
                        $('#cartMethod , #bkashMethod').css("display","none");
                        if(response > 0 ){
                            window.open('/medicine/sales/'+response+'/print', '_blank');
                        }
                        if(response === "failed"){
                            setTimeout(pageReload, 3000);
                        }

                    }
                });
            }
        });

    });

    function pageReload() {
        location.reload();
    }

    $(document).on("click", "#posBtn", function() {

        $('#buttonType').val('posBtn');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $('.salesBtn').prop("disabled", true);
                $.ajax({
                    url         : $('form#salesTemporaryForm').attr( 'action' ),
                    type        : $('form#salesTemporaryForm').attr( 'method' ),
                    data        : new FormData($('form#salesTemporaryForm')[0]),
                    processData : false,
                    contentType : false,
                    success: function(response){
                        $('#salesTemporaryForm')[0].reset();
                        $('#invoiceParticulars').html('');
                        $('#subTotal').html('');
                        $('#grandTotal').html('');
                        $('.discount').html('');
                        $('.dueAmount').html('');
                        $('#profit').html('');
                        $('#salesNetTotal').val('');
                        $('#salesSubTotal').val('');
                        $('#salesTemporary_discount').val('');
                        $('#salesTemporary_due').val('');
                        $('#cartMethod , #bkashMethod').css("display","none");
                        $(".select2TemporaryCustomer").select2("val", "");
                        $(".customer").hide();
                        if(response !== "failed" ){
                            jsPostPrint(response);
                        }else{
                            setTimeout(pageReload, 3000);
                        }

                    }
                });
            }
        });

    });

    function jsPostPrint(data) {

        if(typeof EasyPOSPrinter == 'undefined') {
            alert("Printer library not found");
            return;
        }
        EasyPOSPrinter.raw(data);
        EasyPOSPrinter.cut();
        EasyPOSPrinter.print(function(r, x){
            console.log(r);
        });
    }

    $(".select2StockMedicine").select2({

        placeholder: "Search medicine name",
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
        formatResult: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        formatSelection: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        initSelection: function (element, callback) {
            var id = $(element).val();
            $.ajax(Routing.generate('medicine_stock_name', {vendor: id}), {
                dataType: "json"
            }).done(function (data) {
                return callback(data);
            });
        },
        allowClear: true,
        minimumInputLength:2

    });

    $(".select2TemporaryCustomer").select2({

        ajax: {

            url: Routing.generate('domain_customer_search'),
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
        formatResult: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        formatSelection: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        initSelection: function (element, callback) {
            var customer = $(element).val();
            $.ajax(Routing.generate('domain_customer_name', { customer : customer}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });
        },
        allowClear: true,
        minimumInputLength: 1
    });
}

function jqueryInstantTemporaryLoad(){

    $('#vendor').focus();
    $(document).on('change', '#medicineName', function() {

        var medicine = $('#medicineName').val();
        if(medicine === ""){ return false }
        $.ajax({
            url: Routing.generate('medicine_purchase_particular_search',{'id':medicine}),
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                $('#salesPrice').val(obj['salesPrice']);
                $('#purchasePrice').val(obj['purchasePrice']);
            }
        })

    });

    $('#medicineName').on("select2-selecting", function (e) {
        setTimeout(function () {
            $('#medicineName').focus();
        }, 2000)
    });

    $('form#instantPurchase').on('keyup', '#purchaseQuantity', function (e) {
        var mrp = $('#purchaseQuantity').val();
        $('#salesQuantity').val(mrp);
    });


    $('form#instantPurchase').on('keypress', '.input', function (e) {

        if (e.which === 13) {
            var inputs = $(this).parents("form").eq(0).find("input,select");
            var idx = inputs.index(this);
            if (idx == inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus(); //  handles submit buttons
            }
            switch (this.id) {

                case 'vendor':
                    $('#purchasesBy').select2('open');
                    break;
                case 'purchasesBy':
                    $('#purchaseQuantity').focus();
                    break;
                case 'purchaseQuantity':
                    $('#salesPrice').focus();
                    break;
                case 'salesPrice':
                    $('#addInstantPurchase').click();
                    $('#vendor').focus();
                    break;
            }
            return false;
        }
    });

    $(document).on('click', '#addInstantPurchase', function() {

        var form = $("#instantPurchase").validate({
            rules: {

                "vendor": {required: true},
                "medicineName": {required: true},
                "purchasesBy": {required: false},
                "purchaseQuantity": {required: true},
                "salesPrice": {required: true},
                "expirationStartDate": {required: false},
                "expirationEndDate": {required: false}
            },

            messages: {

                "medicineName": "Enter medicine name",
                "vendor": "Select vendor name",
                "purchasesBy": "Enter purchase by medicine",
                "purchaseQuantity": "Enter medicine quantity",
                "salesPrice": "Enter sales price"
            },
            tooltip_options: {

                "medicineName": {placement: 'top', html: true},
                "vendor": {placement: 'top', html: true},
                "purchasesBy": {placement: 'top', html: true},
                "purchaseQuantity": {placement: 'top', html: true},
                "salesPrice": {placement: 'top', html: true}
            },

            submitHandler: function (form) {
                $.ajax({
                    url: $('form#instantPurchase').attr('action'),
                    type: $('form#instantPurchase').attr('method'),
                    data: new FormData($('form#instantPurchase')[0]),
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        obj = JSON.parse(response);
                        $('#invoiceParticulars').html(obj['salesItems']);
                        $('#subTotal').html(obj['subTotal']);
                        $('#grandTotal').html(obj['initialGrandTotal']);
                        $('.discount').html(obj['initialDiscount']);
                        $('.dueAmount').html(obj['initialGrandTotal']);
                        $('#salesNetTotal').val(obj['initialGrandTotal']);
                        $('#salesTemporary_discount').val(obj['initialDiscount']);
                        $('#salesTemporary_due').val(obj['initialGrandTotal']);
                        $('#instantPurchase')[0].reset();
                        $("#medicineName").select2("val", "");
                        $("#purchasesBy").select2("val", "");
                        $("#medicineId").val('');
                        $('.salesBtn').prop("disabled", false);
                    }
                });
            }
        });
    });

    $(document).on("click", ".instantDelete", function() {

        var url = $(this).attr("data-url");
        var id = $(this).attr("id");
        $.get(url, function(data, status){
            $('#removeInstantItem-'+id).hide();
        });

    });

    $(".select2InstantMedicine").autocomplete({

        source: function( request, response ) {
            $.ajax( {
                url: Routing.generate('medicine_search'),
                data: {
                    term: request.term
                },
                success: function( data ) {
                    response( data );
                }
            } );
        },
        minLength: 2,
        select: function( event, ui ) {
            $("#medicineId").val(ui.item.id); // save selected id to hidden input

        }

    });

    $( ".select2Vendor" ).autocomplete({

        source: function( request, response ) {
            $.ajax( {
                url: Routing.generate('medicine_vendor_search'),
                data: {
                    term: request.term
                },
                success: function( data ) {
                    response(data);
                }
            });
        },
        minLength: 1,
        select: function( event, ui ) {
            $("#vendor").val(ui.item.id); // save selected id to hidden input

        }
    });

    $(".select2User").select2({

        ajax: {
            url: Routing.generate('domain_user_search'),
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
        formatResult: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        formatSelection: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        initSelection: function (element, callback) {
        },
        allowClear: true,
        minimumInputLength: 1
    });

    $( "#expirationStartDate" ).datepicker({
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
    });
    $( "#expirationEndDate" ).datepicker({
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
    });


}






