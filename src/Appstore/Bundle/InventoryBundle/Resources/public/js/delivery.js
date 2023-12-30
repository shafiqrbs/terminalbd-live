/**
 * Created by rbs on 2/9/16.
 */

var InventoryDelivery = function(delivery) {

    $('input[id=delivery_item_purchaseItem]').focus();

    $(document).on('click', '#addDelivery', function() {

        var barcode = $('#delivery_item_purchaseItem').val();
        var quantity = $('#delivery_item_quantity').val();
        if(barcode == ''){
            $('#msg').html('Using wrong barcode, please try again correct barcode.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_delivery_item'),
            type: 'POST',
            data:'barcode='+barcode+'&delivery='+ delivery+'&quantity='+quantity,
            success: function(response) {
                obj = JSON.parse(response);

                $('#delivery_item_quantity').focus().val('');
                $('#deliveryItem').html(obj['deliveryItems']);
                $('#totalQuantity').html(obj['totalQuantity']);
                $('#totalAmount').html(obj['totalAmount']);
                $('#msg').html(obj['msg']);
                FormComponents.init();
            },
        })
    });

    $(document).on('click', '.addSales', function() {

        var barcode = $(this).attr('id');
        var quantity = $('#'+barcode).val();
        alert(quantity);
        if(barcode == ''){
            $('#wrongBarcode').html('Using wrong barcode, please try again correct barcode.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_delivery_item'),
            type: 'POST',
            data:'barcode='+barcode+'&delivery='+ delivery+'&quantity='+quantity,
            success: function(response) {
                obj = JSON.parse(response);
                $('#deliveryItem').html(obj['deliveryItems']);
                $('#totalQuantity').html(obj['totalQuantity']);
                $('#totalAmount').html(obj['totalAmount']);
                FormComponents.init();
            },
        })
    });

    $(document).on('change', '#item', function() {

        var item = $('#item').val();
        if(item == ''){
            $('#stockItemDetails').hide();
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_delivery_item_purchase'),
            type: 'POST',
            data:'item='+ item,
            success: function(response) {
                $('#stockItemDetails').show();
                $('#itemDetails').html(response);
                $(".editable").editable();
            },
        })
    })


    $(document).on("click", ".delete", function() {

        var url = $(this).attr("rel");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                location.reload();
            }
        })
    })

    $(".select2Item").select2({

        placeholder: "Search item, color, size & vendor name",
        allowClear: true,
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

            return item.name +' => '+ (item.remainingQuantity)

        }, // omitted for brevity, see the source of this page
        formatSelection: function(item){return item.name + ' / ' + item.sku}, // omitted for brevity, see the source of this page
        initSelection: function(element, callback) {
            var id = $(element).val();
        },
        allowClear: true,
        minimumInputLength:1
    });

    $("#delivery_item_purchaseItem").select2({

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
        formatResult: function(item){ return item.text +'(' +item.item_name+')'}, // omitted for brevity, see the source of this page
        formatSelection: function(item){return item.text +'(' +item.item_name+')' }, // omitted for brevity, see the source of this page
        initSelection: function(element, callback) {
            var id = $(element).val();
        },
        allowClear: true,
        minimumInputLength:1
    });


}

