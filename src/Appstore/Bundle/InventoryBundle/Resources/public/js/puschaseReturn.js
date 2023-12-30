
var InventoryPurchaseReturnPage = function(purchaseReturn) {

    $('input[name=barcode]').focus();

    $('.purchase').on("click", ".delete", function () {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                location.reload();
            }
        })
    })

    $(document).on("click", ".approvePurchaseReturn", function () {

        var url = $(this).attr("rel");
        var id = $(this).attr("data-title");
        var adjustmentInvoice = $('#adjustmentInvoice').val();
        $.ajax({
            url: url,
            data: 'adjustmentInvoice=' + adjustmentInvoice,
            type: 'GET',
            success: function (response) {
                if(response == 'success'){
                    location.reload();
                }else{
                    alert('Your adjustment GRN no not found');
                }

            },
        })
    })


    $(document).on('change', '#barcode', function () {

        var barcode = $('#barcode').val();
        if (barcode == '') {
            $('#wrongBarcode').html('Using wrong barcode, please try again correct barcode.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_purchasereturn_item_search'),
            type: 'POST',
            data: 'purchaseReturn=' + purchaseReturn + '&barcode=' + barcode,
            success: function (response) {
                $('#barcode').focus().val('');
                obj = JSON.parse(response);
                $('.total').html(obj['total']);
                $('#message').html(obj['message']);
                $('#subItems').html(obj['purchaseReturnItem']);
            },

        })
    })

    $(document).on('click', '#barcodeSearch', function () {

        var barcode = $('#barcodeNo').val();
        if (barcode == '') {
            $('#wrongBarcode').html('Using wrong barcode, please try again correct barcode.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_purchasereturn_item_search'),
            type: 'POST',
            data: 'purchaseReturn=' + purchaseReturn + '&barcode=' + barcode,
            success: function (response) {
                $('#barcodeNo').focus().val('');
                obj = JSON.parse(response);
                if(obj['message'] == 'failed'){
                    $('#message').html('This barcode is not found in this system');
                }else{
                    $('#message').html(obj['message']);
                    $('.total').html(obj['total']);
                    $('#subItems').html(obj['purchaseReturnItem']);
                }

            },

        })
    })

    $(document).on('change', '.quantity , .price', function () {

        var rel = $(this).attr('rel');
        var quantity = $('#quantity-' + rel).val();
        var price = $('#price-' + rel).val();
        var subTotal = (parseInt(quantity) * parseInt(price));
        $("#subTotalShow-" + rel).html(subTotal);
        $.ajax({
            url: Routing.generate('inventory_purchasereturn_item_update'),
            type: 'POST',
            data: 'itemId=' + rel + '&quantity=' + quantity + '&price=' + price,
            success: function (response) {
                obj = JSON.parse(response);
                $('#message').html(obj['message']);
                $('.total').html(obj['total']);
            },
        })
    })

    $(document).on('click', '.replace', function () {

        var rel = $(this).attr('rel');
        var curQuantity = $('#curQuantity-' + rel).val();
        var quantity = $('#quantity-' + rel).val();
        var price = $('#price-' + rel).val();
        var subTotal = ( (parseInt(curQuantity) - parseInt(quantity)) * parseInt(price));
        if (curQuantity == 0 || parseInt(quantity) > parseInt(curQuantity)) {

            $('#quantity-' + rel).val(curQuantity);
            $('#quantity-' + rel).focus();
            return false;
        }
        $("#curQnt-" + rel).html(parseInt(curQuantity) - parseInt(quantity));
        $("#subTotal-" + rel).html(subTotal);
        $.ajax({
            url: Routing.generate('inventory_purchasereturn_item_replace'),
            type: 'POST',
            data: 'itemId=' + rel + '&quantity=' + quantity,
            success: function (response) {
                obj = JSON.parse(response);
                if ('success' == obj['success']) {
                    location.reload();
                    $('#action-' + rel).append('<a  href="javascript:" class="btn purple mini" ><i class="icon-check"></i>&nbsp;Replaced</a>');
                }
            },
        })
    })

    $('.replaceItem').on("click", ".delete", function () {
        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                location.reload();
            },
        })
    })

    $('.replaceItem').on("click", ".purchaseReturnDelete", function () {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                window.location.replace("/inventory/purchase/return");
            },
        })
    })

}
