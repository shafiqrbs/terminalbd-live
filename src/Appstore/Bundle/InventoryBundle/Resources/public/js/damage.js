
/**
 * Created by rbs on 2/9/16.
 */

var InventoryDamage = function() {

    $('#barcodeNo').focus();
    /*$(document).on('change', '#barcode', function() {
        var barcode = $('#barcode').val();
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
                $('#purchaseItem').html(obj['purchaseItem']);
                $('#salesItem').html(obj['salesItem']);
                $('.salesTotal').html(obj['salesTotal']);
                $('.salesTotal').val(obj['salesTotal']);
                $('#paymentTotal').val(obj['salesTotal']);
                $('#paymentSubTotal').val(obj['salesTotal']);
                FormComponents.init();
            },

        })
    })

    $(document).on('change', '#barcodeNo', function() {

        var barcode = $('#barcodeNo').val();
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
                $('#purchaseItem').html(obj['purchaseItem']);
                $('#salesItem').html(obj['salesItem']);
                $('.salesTotal').html(obj['salesTotal']);
                $('.salesTotal').val(obj['salesTotal']);
                $('#paymentTotal').val(obj['salesTotal']);
                $('#paymentSubTotal').val(obj['salesTotal']);
                FormComponents.init();
            },
        })
    })*/

    $("#appstore_bundle_inventorybundle_Damage_purchaseItem").select2({
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
        formatResult: function(item){ return item.text + item.item_name}, // omitted for brevity, see the source of this page
        formatSelection: function(item){return item.text + item.item_name }, // omitted for brevity, see the source of this page
        initSelection: function(element, callback) {
            var id = $(element).val();


        },
        allowClear: true,
        minimumInputLength:1
    });


}

