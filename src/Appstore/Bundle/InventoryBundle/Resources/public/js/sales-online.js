/**
 * Created by rbs on 2/9/16.
 */

/*function initializeAutoTabOptionOnEnterKey($form) {
    $('#particular').select2('open');
    $('#particular').on("select2-selecting", function (e) {
        setTimeout(function () {
            gotoNextFieldOfParticular();
        }, 50)
    });

    $form.on('keypress', 'input', function (e) {

        if (e.which == 13) {
            e.preventDefault();

            switch (this.id) {
                case 'particular':
                    gotoNextFieldOfParticular();
                    break;
                case 'description':
                    $amountField.focus();
                    break;
                case 'amount':
                    $form.find('input.btn').trigger('click');
                    break;
            }
        }
    });
}*/

$(document).on("click", ".paymentBtn", function (e) {

    var total =  parseInt($('#netTotal').val());
    if(total === 0 ){
        alert('Please add sales item');
        $('#salesitem_item').focus();
        return false;
    }

    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function (event, el) {
            $('form').submit();
        }
    });
    e.preventDefault();
});

$(".select2Customer").select2({

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
    minimumInputLength: 2
});

$('form#salesForm').on('keypress', '.inputs', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);

        if (idx === inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {
            case 'sales_discount':
                $('#sales_payment').focus();
                break;
            case 'mobile':
                $('#onlineSalesPos').focus();
                break;
        }
        return false;
    }
});


var InventorySales = function(sales) {

    $('#item').select2('open');

    $(".addCustomer").click(function(){
        $( ".customer" ).slideToggle( "slow" );
    }).toggle( function() {
        $(this).removeClass("blue").addClass("red").html('<i class="icon-remove"></i>');
    }, function() {
        $(this).removeClass("red").addClass("blue").html('<i class="icon-user"></i>');
    });


    $( "#searchToggole" ).click(function() {
        $( "#search-product" ).slideToggle( "slow" );
    });

    $('form.horizontal-form').on('keypress', 'input', function (e) {

        if (e.which === 13) {
            e.preventDefault();

            switch (this.id) {

                case 'discount':
                    $('#paymentAmount').focus();
                    break;

                case 'paymentAmount':
                    $('#mobile').select2('open');
                    $('#sales_mobile').focus();
                    $(".paymentBtn").attr("disabled", false);
                    break;
            }
        }
    });

    $('#sales_general_salesBy').on("select2-selecting", function (e) {
        setTimeout(function () {
            $('#discount').focus();
        }, 2000)
    });

    function resultDataLoad(data){
        obj = JSON.parse(data);
        $('.subTotal').html(obj['salesSubTotal']);
        $('.discount').html(obj['discount']);
        $('.vat').html(obj['salesVat']);
        $('.salesTotal').html(obj['salesTotal']);
        $('#paymentSubTotal').val(obj['salesTotal']);
        $('#due').val(obj['due']);
        $('.dueAmount').html(obj['due']);
        $('#paymentTotal').val(obj['salesTotal']);
        $('#wrongBarcode').html(obj['msg']);
    }

    $(document).on('change', '#barcode', function(e) {
        e.preventDefault();
        var barcode = $('#barcode').val();
        if(barcode === ''){
            $('#wrongBarcode').html('<strong>Error!: </strong>Invalid barcode, Please try again.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_item_search'),
            type: 'POST',
            data:'barcode='+barcode+'&sales='+sales,
            success: function(response){
                url = Routing.generate('inventory_sales_item_data');
                $.get(url,{'id':sales},  // url
                    function (response) {  // success callback
                        $('#barcode').focus().val('');
                        obj = JSON.parse(response);
                        $('#salesItem').html(obj['salesItems']);
                        resultDataLoad(response);
                        FormComponents.init();
                });
            },

        })
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
        })
    });

    $(document).on('change', '#barcodeNo', function(e) {

        var barcode = $('#barcodeNo').val();
        if(barcode === ''){
            $('#wrongBarcode').html('Using wrong barcode, Please try again correct barcode.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_purchase_item_search'),
            type: 'POST',
            data:'barcode='+barcode+'&sales='+ sales,
            success: function(response) {
                $('#barcode').focus().val('');
                obj = JSON.parse(response);
                $('#salesItem').html(obj['salesItems']);
                resultDataLoad(response);
                FormComponents.init();
            },
        })
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
        })
    });

    $(document).on('click', '.addSales', function() {

        var barcode = $(this).attr('id');
        if(barcode === ''){
            $('#wrongBarcode').html('Using wrong barcode, please try again correct barcode.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_item_search'),
            type: 'POST',
            data:'barcode='+barcode+'&sales='+ sales,
            success: function(response) {
                $('#barcode').focus().val('');
                obj = JSON.parse(response);
                $('#salesItem').html(obj['salesItems']);
                resultDataLoad(response);
                FormComponents.init();
            },
        })
    });

    $("#stockItem").submit(function(e) {

        var item = $('#salesitem_item').val();
        var url =  $('#stockItem').attr("action");
        if(item === ''){
            alert('Please try again correct product.');
            return false;
        }
        $.ajax({
            url         : url,
            type        : 'POST',
            data        : new FormData($('form#stockItem')[0]),
            processData : false,
            contentType : false,
            success     : function(response){
                obj = JSON.parse(response);
                $('#quantity').val(1);
                $('#item').select2('open');
                $('#salesItem').html(obj['salesItems']);
                resultDataLoad(response);
            }
        });
        e.preventDefault();
    });


    $(document).on('click', '.addPurchaseItemSales', function() {

        var barcode = $(this).attr('id');
        if(barcode == ''){
            $('#wrongBarcode').html('Using wrong barcode, please try again correct barcode.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_item_search'),
            type: 'POST',
            data:'barcode='+barcode+'&sales='+ sales,
            success: function(response) {
                $('#barcode').focus().val('');
                obj = JSON.parse(response);
                $('#salesItem').html(obj['salesItems']);
                resultDataLoad(response);
                FormComponents.init();
            },
        })
    });

    $(document).on('change', '#salesitem_item', function() {

        var item = $(this).val();
        $.ajax({
            url: Routing.generate('pos_item_barcode_info'),
            type: 'POST',
            data:'item='+item+'&barcode=0',
            success: function(response){
                obj = JSON.parse(response);
                $('#current-stock').html(obj['quantity']);
                $('#avg-price').html(obj['purchase']);
                $('#sales-price').html(obj['price']);
                $('#item-status').html(obj['status']);
                $('.purchaseItem').html(obj['purchaseItem']);
                $('#serialNo').html(obj['serialNo']);
                $('#price').val(obj['price']);
                $('#quantity').focus();

            },
        })
    });

    $('form#stockItem').on('keypress', '.stockItem', function (e) {

        if (e.which === 13) {
            var inputs = $(this).parents("form#stockItem").eq(0).find("input,select");
            var idx = inputs.index(this);

            if (idx === inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus(); //  handles submit buttons
            }
            switch (this.id) {
                case 'item':
                    $('#quantity').focus();
                    break;

                case 'quantity':
                    $('#salesPrice').focus();
                    break;

                case 'salesPrice':
                    $('#addItem').click();
                    $('#item').select2('open');
                    break;

            }
            return false;
        }
    });


    $(document).on('change', '#item', function() {

        var item = $('#item').val();
        if(item === ''){
            $('#stockItemDetails').hide();
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_item_purchase'),
            type: 'POST',
            data:'item='+ item +'&sales=' + sales,
            success: function(response) {
                $('#stockItemDetails').show();
                $('#itemDetails').html(response);
                $(".editable").editable();
            },
        })

    });


    $(document).on('change', '.quantity , .salesPrice', function() {

        var rel = $(this).attr('rel');

        var quantity = parseFloat($('#quantity-'+rel).val());
        var price = parseFloat($('#salesPrice-'+rel).val());
        var subTotal  = (quantity * price);
        $("#subTotalShow-"+rel).html(subTotal);
        $.ajax({
            url: Routing.generate('inventory_sales_item_update'),
            type: 'POST',
            data:'salesItemId='+ rel +'&quantity='+ quantity +'&salesPrice='+ price +'&customPrice='+ price,
            success: function(response) {
                obj = JSON.parse(response);
                $('#salesItem').html(obj['salesItems']);
                resultDataLoad(response);
            },

        })
    });

    $(document).on('click', '.serialSave', function() {
        var id =$(this).attr('id');
        var url =$(this).attr('data-url');
        var serial = $('#serialNo-'+id).val();
        if(serial ==='undefined') {
            alert('Please select serial no.');
            return false;
        }else{
            $.get(url,{'serial':serial});
        }

    });


    $(document).on("click", ".delete", function(event) {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $(event.target).closest('tr').remove();
                obj = JSON.parse(response);
                if ('success' === obj['success']) {
                    resultDataLoad(response);
                }
            },
        })

    });

    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();

    $(document).on('change', '#sales_discountCalculation', function() {

        var discountType = $('#sales_discountType').val();
        var discount = parseFloat($('#sales_discountCalculation').val());
        var invoice = $('#salesId').val();
        var total =  parseFloat($('#paymentTotal').val());
        if( discount >= total ){
            $('#sales_discount').val(0);
            alert ('Discount amount less then total amount');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_discount_update'),
            type: 'POST',
            data:'discount=' + discount+'&discountType='+discountType+'&sales='+invoice,
            success: function(response) {
                resultDataLoad(response);
            }

        })

    });

    $(document).on('change', '#sales_discountType', function() {

        var discountType = $('#sales_discountType').val();
        var discount = parseFloat($('#sales_discountCalculation').val());
        var invoice = $('#salesId').val();
        var total =  parseFloat($('#paymentTotal').val());
        if( discount >= total ){
            $('#sales_discount').val(0);
            alert ('Discount amount less then total amount');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_discount_update'),
            type: 'POST',
            data:'discount=' + discount+'&discountType='+discountType+'&sales='+invoice,
            success: function(response) {
                resultDataLoad(response);
            }

        })

    });

    $(document).on('keyup', '#sales_payment', function() {

        var payment     = parseInt($('#sales_payment').val()  !== '' ? $('#sales_payment').val() : 0 );
        var total =  parseInt($('#paymentTotal').val());
        var dueAmount = (total-payment);
        if(dueAmount > 0){
            $('#balance').html('Due Tk.');
            $('.dueAmount').html(dueAmount);
            $('#due').val(dueAmount);
        }else{
            var balance =  payment - total ;
            $('#balance').html('Return TK.');
            $('.dueAmount').html(balance);
            $('#due').html(balance);
        }

    });

    $(document).on("click", ".receiveBtn", function() {

        if ($('#paymentTotal').val().length === 0){
            alert('Please add sales item');
            $('input[name=barcode]').focus();
            return false;
        }

        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $('form#salesForm').submit();
            }
        });

    });

    $(document).on('change', '#sales_transactionMethod', function() {

        var paymentMethod = $(this).val();
        if( paymentMethod === 2){
            $('#cartMethod').show();
            $('#bkashMethod').hide();
        }else if( paymentMethod === 3){
            $('#bkashMethod').show();
            $('#cartMethod').hide();
        }else{
            $('#cartMethod').hide();
            $('#bkashMethod').hide();
        }

    });


    $(".select2Item").select2({

        placeholder: "Search item, color, size & brand name",
        ajax: {
            url: Routing.generate('item_search'),
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
        formatResult: function(item){

            //return item.name +' => '+ (item.remainingQuantity)
            return item.name

        }, // omitted for brevity, see the source of this page
        formatSelection: function(item){return item.name}, // omitted for brevity, see the source of this page
        initSelection: function(element, callback) {
            var id = $(element).val();
        },
        allowClear: true,
        minimumInputLength:1
    });

    $(".select2Location").select2({

        placeholder: "Search location name",
        ajax: {
            url: Routing.generate('inventory_location_search'),
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
        formatResult: function(item){
            return item.text

        }, // omitted for brevity, see the source of this page
        formatSelection: function(item){return item.text}, // omitted for brevity, see the source of this page
        initSelection: function(element, callback) {
            var id = $(element).val();
        },
        allowClear: true,
        minimumInputLength:1
    });

    $("#barcodeNo").select2({

        placeholder: "Enter specific barcode",
        allowClear: true,
        ajax: {

            url: Routing.generate('inventory_purchaseitem_search'),
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
        formatResult: function(item){ return item.text}, // omitted for brevity, see the source of this page
        formatSelection: function(item){return item.text }, // omitted for brevity, see the source of this page
        initSelection: function(element, callback) {
            var id = $(element).val();
        },
        minimumInputLength:1
    });


}

