function InventoryPurchasePage(){

    var initEditables = function () {
        //global settings
        $.fn.editable.defaults.inputclass = 'm-wrap';
        $.fn.editable.defaults.url = '/post';
        $.fn.editableform.buttons = '<button type="submit" class="btn blue editable-submit"><i class="icon-ok"></i></button>';
        $.fn.editableform.buttons += '<button type="button" class="btn editable-cancel"><i class="icon-remove"></i></button>';
    }
    $(".editable").editable(initEditables());
     $(document).on("click", ".editable-submit", function() {
         setTimeout(pageReload, 1000);
     });
     function pageReload() {
         //location.reload();
     }
    $( "#masterItem" ).autocomplete({
        source: function( request, response ) {
            $.ajax( {
                url: Routing.generate('inventory_product_masteritem_search'),
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
        }
    });

    $( ".select2barcode" ).autocomplete({
        source: function( request, response ) {
            $.ajax( {
                url: Routing.generate('inventory_product_masteritem_search'),
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
        }
    });

    $('#addMasterItem').click(function(e) {

        var url =  $('#masterProduct').attr("action");
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data : $('#masterProduct').serialize(),
                    success: function (response) {
                        location.reload();
                    },
                });
            }
        });
        e.preventDefault();
    });

    $('#purchaseitem_item').on("select2-selecting", function (e) {
        setTimeout(function () {
            $('#purchaseitem_purchasePrice').focus();
        }, 100)
    });

    $('form#purchaseItemForm').on('keypress', '.itemInput', function (e) {

        if (e.which === 13) {
            var inputs = $(this).parents("form").eq(0).find("input,select");
            var idx = inputs.index(this);
            if (idx == inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus(); //  handles submit buttons
            }
            switch (this.id) {
                case 'purchaseitem_quantity':
                    $('#addPurchaseForm').focus();
                    break;
            }
            return false;
        }
    });

    $('#barcode').focus();

    $(document).on('change', '#barcode', function() {

        var barcode = $('#barcode').val();
        var id = $(this).attr('data-id');
        if(barcode === ''){
            $('#wrongBarcode').html('<strong>Error!: </strong>Invalid barcode, Please try again.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_purchasesimple_barcode_insert',{'id':id}),
            type: 'POST',
            data:'barcode='+barcode,
            success: function(response){
                obj = JSON.parse(response);
                $('#purchaseItem').html(obj['invoiceItems']);
                $('.subTotal').html(obj['subTotal']);
                $('#due').html(obj['due']);
                $('#barcode').focus().val('');
            },

        });
        $.ajax({
            url: Routing.generate('pos_item_barcode_info'),
            type: 'POST',
            data:'item=0&barcode='+barcode,
            success: function(response){
                obj = JSON.parse(response);
                $('#current-stock').html(obj['quantity']);
                $('#avg-price').html(obj['purchase']);
                $('#sales-price').html(obj['price']);
                $('#item-status').html(obj['status']);
            },
        });
    });

    var validator =  $('form#purchaseItemForm').validate({

        rules: {

            "purchaseitem[item]": {required: true},
            "purchaseitem[purchaseSubTotal]": {required: false},
            "purchaseitem[salesPrice]": {required: false},
            "purchaseitem[quantity]": {required: true},
        },

        messages: {

            "purchaseitem[item]":"Select purchase item name",
            "purchaseitem[purchaseSubTotal]":"Enter total purchase price",
            "purchaseitem[quantity]":"Enter product qnt",
        },

        tooltip_options: {

            "purchaseitem[item]": {placement:'top',html:true},
            "purchaseitem[purchaseSubTotal]": {placement:'top',html:true},
            "purchaseitem[quantity]": {placement:'top',html:true},

        },
        submitHandler: function(validator) {
            $.ajax({
                url         : $('form#purchaseItemForm').attr( 'action' ),
                type        : $('form#purchaseItemForm').attr( 'method' ),
                data        : new FormData($('form#purchaseItemForm')[0]),
                processData : false,
                contentType : false,
                success: function(response){
                    obj = JSON.parse(response);
                    $('#purchaseItem').html(obj['invoiceItems']);
                    $('.subTotal').html(obj['subTotal']);
                    $('#due').html(obj['due']);
                    $("#purchaseitem_item").select2("val", "");
                    $('#purchaseItemForm')[0].reset();
                    $('#addPurchaseForm').html('<i class="fa fa-save"></i> Add').attr("disabled", true);
                    $('.addPurchaseForm').prop("disabled", false);
                    $(".editable").editable(initEditables());
                }
            });
        }

    });

    $(document).on('change', '.itemUpdate', function() {

        var id = $(this).attr('data-id');
        var quantity = parseFloat($('#quantity-'+id).val());
        var price = parseFloat($('#price-'+id).val());
        var salesPrice = parseFloat($('#salesPrice-'+id).val());
        var subTotal  = (quantity * price);
        $("#subTotal-"+id).html(subTotal);
        $.ajax({
            url: Routing.generate('inventory_purchasesimple_inline_item_update'),
            type: 'POST',
            data:'id='+id+'&quantity='+ quantity +'&price='+ price+'&salesPrice='+ salesPrice,
            success: function(response) {
                $(".editable").editable(initEditables());
                obj = JSON.parse(response);
                $('#purchaseItem').html(obj['invoiceItems']);
                $('.subTotal').html(obj['subTotal']);
                $('#due').html(obj['due']);
                $("#salesTemporaryItem_stockName").select2("val", "");
                $('#purchaseitem')[0].reset();

            },

        })
    });

    $('#addInventory').click(function(e) {
        $( "#inventoryItem" ).fadeToggle();
    });


    $(document).on("click", ".vendorItemDelete", function() {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response) {
                   location.reload();
                }
            },
        })
    });

    $(document).on("click", ".itemDelete", function(event) {
        var url = $(this).attr("data-url");
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                obj = JSON.parse(response);
                $('.subTotal').html(obj['subTotal']);
                $('#due').html(obj['due']);
                $(event.target).closest('tr').hide();
            }
        })
    });

    $('#addStockItem').click(function(e) {

        var url =  $('#inventoryItem').attr("action");
        var masterItem = $('#masterItem').val();
        if( masterItem === "" ){
            alert( "Please add  item name" );
            $('#masterItem').focus();
            return false;
        }
        var color = $('#color').val();
        if( color === "" ){
            alert( "Please add color name" );
            $('#color').focus();
            return false;
        }
        var size = $('#size').val();
        if( size === "" ){
            alert( "Please add size value" );
            $('#size').focus();
            return false;
        }

        var brand = $('#brand').val();
        if( brand === "" ){
            alert( "Please add brand name" );
            $('#brand').focus();
            return false;
        }

        var vendor = $('#vendor').val();
        if( vendor === "" ){
            alert( "Please add vendor name" );
            $('#vendor').focus();
            return false;
        }
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data : $('#inventoryItem').serialize(),
                    success: function (response) {
                        obj = JSON.parse(response);
                        if(obj['status'] == 'valid'){
                            location.reload();
                        }else{
                            alert(obj['message']);
                        }
                    },
                });
            }
        });
        e.preventDefault();
    });

    $(document).on("click", ".purchaseItemDelete", function() {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response) {
                   location.reload();

                }
            },
        })
    });

    $('#purchase_totalItem').attr("readonly", true);
    $('#purchase_totalQnt').attr("readonly", true);
    $('#purchase_dueAmount').attr("readonly", true);
    $('#purchase_totalAmount').attr("readonly", true);

    $('form#purchaseForm').on('keypress', '.purchaseInput', function (e) {

        if (e.which === 13) {
            var inputs = $(this).parents("form").eq(0).find("input,select");
            var idx = inputs.index(this);
            if (idx == inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus(); //  handles submit buttons
            }
            switch (this.id) {
                case 'purchase_purchaseTo':
                    $('#purchase_memo').focus();
                    break;

                case 'purchase_paymentAmount':
                    $('#purchase_process').focus();
                    break;

                case 'purchase_process':
                    $('#actionButton').focus();
                    break;
            }
            return false;
        }
    });


    $("form#purchaseFormxxx").validate({

        rules: {

            "purchase[vendor]": {required: true},
            "purchase[memo]": {required: false},
            "purchase[totalItem]": {required: false},
            "purchase[totalQnt]": {required: false},
            "purchase[totalAmount]": {required: true},
            "purchase[dueAmount]": {required: false},
            "purchase[paymentAmount]": {required: false},
            "purchase[accountBank]": {required: false},
            "purchase[accountMobileBank]": {required: false},
            "purchase[file]": {required: false},
        },

        messages: {

            "purchase[vendor]":"Enter vendor name",
            "purchase[memo]":"Enter memo or invoice no",
            "purchase[receiveDate]":"Enter receive date",
        },

        tooltip_options: {

            "purchase[vendor]": {placement:'top',html:true},
            "purchase[memo]": {placement:'top',html:true},
            "purchase[totalItem]": {placement:'top',html:true},
            "purchase[totalQnt]": {placement:'top',html:true},
            "purchase[totalAmount]": {placement:'top',html:true},
        }

    });

    $('#purchase_totalAmount , #purchase_paymentAmount ,#purchase_commissionAmount ').change(function() {

        var totalAmount = ($('#purchase_totalAmount').val());
        total = (totalAmount != '') ? parseInt(totalAmount) : 0;
        var paymentAmount = $('#purchase_paymentAmount').val();
        payment = ( paymentAmount != '') ? parseInt(paymentAmount) : 0;
        var commissionPayment = ($('#purchase_commissionAmount').val());
        commission = (commissionPayment != '') ? parseInt(commissionPayment) : 0;
        //var due = (total -  ( payment + commission));
        var due = (total - payment);
        $('#purchase_dueAmount').val(due);
        if (paymentAmount = ""){
            alert(total);
            $('#purchase_paymentAmount').val(total);
        }

    });


}

