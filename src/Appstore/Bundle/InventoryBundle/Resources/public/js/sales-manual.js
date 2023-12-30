$('#addItem').attr("disabled", true);
$(".addCustomer").click(function(){
    $( ".customer" ).slideToggle( "slow" );
}).toggle( function() {
    $(this).removeClass("blue").addClass("red").html('<i class="icon-remove"></i>');
}, function() {
    $(this).removeClass("red").addClass("blue").html('<i class="icon-user"></i>');
});

var InventorySales = function(sales) {

    $(".select2CustomerName").select2({

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
            $.ajax(Routing.generate('domain_customer_name', {customer: customer}), {
                dataType: "json"
            }).done(function (data) {
                return callback(data);
            });
        },
        allowClear: true,
        minimumInputLength: 3
    });

    $(document).on("click", ".remove,.itemRemove", function (event) {

        var url = $(this).attr('data-url');
        var id = $(this).attr('id');
        $.get(url, function (response) {
            $(event.target).closest('tr').remove();
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#netTotal').html(obj['netTotal']);
            $('#vat').html(obj['vat']);
            $('#discount').html(obj['discount']);
            $('#due').html(obj['due']);
            $('#total').val(obj['netTotal']);
        });

    });

    $(document).on('change', '#barcode', function(e) {

        e.preventDefault();
        var barcode = $('#barcode').val();
        var sales  = $(this).attr('data-id');
        if(barcode === ''){
            $('#wrongBarcode').html('<strong>Error!: </strong>Invalid barcode, Please try again.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_salesmanual_item_barcode_search'),
            type: 'POST',
            data:'barcode='+barcode+'&sales='+ sales,
            success: function(response){
                $('#barcode').focus().val('');
                obj = JSON.parse(response);
                $('#salesItem').html(obj['salesItems']);
                $('#subTotal').html(obj['subTotal']);
                $('#netTotal').html(obj['netTotal']);
                $('#vat').html(obj['vat']);
                $('#discount').html(obj['discount']);
                $('#due').html(obj['due']);
                $('#total').val(obj['netTotal']);
                $('#addItem').attr("disabled", true);
                $('#salesPrice').val('');
                $('#quantity').val('1');
            },

        })
    });

    $(document).on('change', '.stockItem', function (e) {
        e.preventDefault();
        var item = $(this).val();
        $.ajax({
            url: Routing.generate('inventory_salesmanual_item_search'),
            type: 'POST',
            data: 'item=' + item,
            success: function (response) {
                var obj = JSON.parse(response);
                $('#itemId').val(obj['itemId']);
                $('#salesPrice').val(obj['price']);
                $('#purchasePrice').val(obj['purchasePrice']);
                if (obj['unit']) {
                    $('.unit').html(obj['unit']);
                }
                $('#addItem').attr("disabled", false);
            }
        })
    });

    $(document).on('click', '#addItem', function (e) {

        var url = $(this).attr('data-url');
        var item = $('#salesitem_item').val();
        var salesPrice = $('#salesPrice').val();
        var quantity = $('#quantity').val();
        var purchasePrice = $('#purchasePrice').val();
        if (item == '') {
            alert('You have to select item from drop down and this not item');
            return false;
        }
        if (quantity == '') {
            alert('Add Product Price & Quantity , Please try again.');
            return false;
        }
        $.ajax({
            url: url,
            type: 'POST',
            data: 'item=' + item + '&quantity=' + quantity + '&salesPrice=' + salesPrice + '&purchasePrice=' + purchasePrice,
            success: function (response) {
                obj = JSON.parse(response);
                $('#salesItem').html(obj['salesItems']);
                $('#subTotal').html(obj['subTotal']);
                $('#netTotal').html(obj['netTotal']);
                $('#vat').html(obj['vat']);
                $('#discount').html(obj['discount']);
                $('#due').html(obj['due']);
                $('#total').val(obj['netTotal']);
                $('#addItem').attr("disabled", true);
                $('#salesPrice').val('');
                $('#quantity').val('1');
            },
        })
        e.preventDefault();

    });

    $(document).on('change', '.quantity , .salesPrice', function() {

        var itemId = $(this).attr('data-id');
        var quantity = parseFloat($('#quantity-'+itemId).val());
        var price = parseFloat($('#salesPrice-'+itemId).val());
        var subTotal  = (quantity * price);
        $("#subTotalShow-"+itemId).html(subTotal);

        $.ajax({
            url: Routing.generate('inventory_salesmanual_item_update'),
            type: 'POST',
            data:'itemId='+ itemId +'&quantity='+ quantity +'&salesPrice='+ price,
            success: function(response) {
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('#netTotal').html(obj['netTotal']);
                $('#vat').html(obj['vat']);
                $('#discount').html(obj['discount']);
                $('#due').html(obj['due']);
                $('#total').val(obj['netTotal']);
                $('#wrongBarcode').html(obj['msg']);
            },

        })
    });

/*    $(document).on('change', '#sales_discountxx', function () {

        var discount = parseInt($('#sales_discount').val());
        var total = parseInt($('#netTotal').val());
        alert(total);
        if (discount >= total) {
            $('#sales_discount').val(0);
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_salesmanual_discount_update'),
            type: 'POST',
            data: 'discount=' + discount + '&sales=' + sales,
            success: function (response) {
                obj = JSON.parse(response);
                $('#subTotal').val(obj['subTotal']);
                $('#netTotal').val(obj['netTotal']);
                $('#vat').val(obj['vat']);
                $('#dueAmount').val(obj['netTotal']);
            },

        })
    });

    $(document).on('keyup', '#sales_discount', function() {

        var discount = parseInt($('#sales_discount').val());
        var total =  parseInt($('#paymentTotal').val());
        if(discount >= total ){
            $('#sales_discount').val(0);
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_salesmanual_discount_update'),
            type: 'POST',
            data:'discount=' + discount+'&sales='+ sales,
            success: function(response) {
                obj = JSON.parse(response);
                $('#subTotal').val(obj['subTotal']);
                $('#netTotal').val(obj['netTotal']);
                $('#vat').val(obj['vat']);
                $('#dueAmount').val(obj['netTotal']);
            },

        })
    });*/

    $(document).on('change', '.salesOperation', function() {

        var discountType = $('#sales_discountType').val();
        var discount = parseFloat($('#sales_discountCalculation').val());
        var deliveryCharge = parseFloat($('#sales_deliveryCharge').val());
        var invoice = $('#salesId').val();
        var total =  parseFloat($('#total').val());
        if( discount >= total ){
            $('#sales_discount').val(0);
            alert ('Discount amount less then total amount');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_salesmanual_discount_update'),
            type: 'POST',
            data:'discount=' + discount+'&discountType='+discountType+'&deliveryCharge='+deliveryCharge+'&sales='+invoice,
            success: function(response) {
                obj = JSON.parse(response);
                $('#salesItem').html(obj['salesItems']);
                $('#subTotal').html(obj['subTotal']);
                $('#netTotal').html(obj['netTotal']);
                $('#vat').html(obj['vat']);
                $('#discount').html(obj['discount']);
                $('#due').html(obj['due']);
                $('#total').val(obj['netTotal']);
                $('#wrongBarcode').html(obj['msg']);
            }

        })

    });

    $(document).on('change', '#sales_discountTypexx', function() {

        var discountType = $('#sales_discountType').val();
        var discount = parseFloat($('#sales_discountCalculation').val());
        var invoice = $('#salesId').val();
        var total =  parseFloat($('#total').val());
        if( discount >= total ){
            $('#sales_discount').val(0);
            alert ('Discount amount less then total amount');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_salesmanual_discount_update'),
            type: 'POST',
            data:'discount=' + discount+'&discountType='+discountType+'&sales='+invoice,
            success: function(response) {
                obj = JSON.parse(response);
                $('#salesItem').html(obj['salesItems']);
                $('#subTotal').html(obj['subTotal']);
                $('#netTotal').html(obj['netTotal']);
                $('#vat').html(obj['vat']);
                $('#discount').html(obj['discount']);
                $('#due').html(obj['due']);
                $('#total').html(obj['netTotal']);
                $('#wrongBarcode').html(obj['msg']);
            }

        })

    });


    $(document).on('keyup', '#sales_payment', function () {

        var payment     = parseInt($('#sales_payment').val() != '' ? $('#sales_payment').val() : 0 );
        var total       =  parseInt($('#total').val());
        var dueAmount   = (total-payment);
        if(dueAmount > 0){
            $('#balance').html('DUE TK.');
            $('#due').html(dueAmount);
        }else{
            var balance =  payment - total ;
            $('#balance').html('RETURN TK.');
            $('#due').html(balance);
        }

    });

    $(document).on("click", ".btn-qnt-particular", function (e) {

        e.preventDefault();
        var productId = $(this).attr('data-text');
        var price = $(this).attr('data-title');
        fieldId = $(this).attr('data-id');
        fieldName = $(this).attr('data-field');
        type = $(this).attr('data-type');
        var input = $('#quantity');
        var currentVal = parseInt(input.val());
        if (!isNaN(currentVal)) {
            if (type == 'minus') {
                if (currentVal > input.attr('min')) {
                    var existVal = (currentVal - 1);
                    input.val(existVal).change();
                }
                if (parseInt(input.val()) == input.attr('min')) {
                    $(this).attr('disabled', true);
                }

            } else if (type == 'plus') {

                if (currentVal < input.attr('max')) {
                    var existVal = (currentVal + 1);
                    input.val(existVal).change();
                }
                if (parseInt(input.val()) == input.attr('max')) {
                    $(this).attr('disabled', true);
                }

            }
        } else {
            input.val(1);
        }

    });

    $('form#salesForm').on('keypress', '.inputs', function (e) {

        if (e.which === 13) {
            var inputs = $(this).parents("form").eq(0).find("input,select");
            var idx = inputs.index(this);
            if (idx == inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus(); //  handles submit buttons
            }
            switch (this.id) {
                case 'sales_payment':
                    $('#sales_process').focus();
                    break;
                case 'sales_process':
                    $('#submitBtn').focus();
                    break;
            }
            return false;
        }
    });

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
}

