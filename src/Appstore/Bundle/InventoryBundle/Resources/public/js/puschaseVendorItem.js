
var InventoryPurchaseVendorItemPage = function (purchaseItemUrl) {

    $('#appstore_bundle_inventorybundle_purchasevendoritem_quantity , #appstore_bundle_inventorybundle_purchasevendoritem_purchasePrice ,#appstore_bundle_inventorybundle_purchasevendoritem_salesPrice, #appstore_bundle_inventorybundle_purchasevendoritem_webPrice ').change(function () {

        var quantity = ($('#appstore_bundle_inventorybundle_purchasevendoritem_quantity').val());
        quantity = (quantity != '') ? parseInt(quantity) : 0;

        var purchasePrice = ($('#appstore_bundle_inventorybundle_purchasevendoritem_purchasePrice').val());
        purchasePrice = (purchasePrice != '') ? parseInt(purchasePrice) : 0;
        var purchaseSubtotal = ( quantity * purchasePrice);
        if (purchasePrice > 0) {
            $('#appstore_bundle_inventorybundle_purchasevendoritem_subTotalPurchasePrice').val(purchaseSubtotal);
        }

        var salesPrice = ($('#appstore_bundle_inventorybundle_purchasevendoritem_salesPrice').val());
        salesPrice = (salesPrice != '') ? parseInt(salesPrice) : 0;
        var salesSubtotal = ( quantity * salesPrice);
        if (salesPrice > 0) {
            $('#appstore_bundle_inventorybundle_purchasevendoritem_subTotalSalesPrice').val(salesSubtotal);
        }
/*

        var webPrice = ($('#appstore_bundle_inventorybundle_purchasevendoritem_webPrice').val());
        webPrice = (webPrice != '') ? parseInt(webPrice) : 0;
        var subTotalWebPrice = ( quantity * webPrice);
        if (webPrice > 0) {
            $('#appstore_bundle_inventorybundle_purchasevendoritem_subTotalWebPrice').val(subTotalWebPrice);
        }
*/


    })


    $('.action').submit(function (e) {

        var number_regex = /^[0-9]+$/;

        var name = $('#appstore_bundle_inventorybundle_purchasevendoritem_name').val();
        var quantity = $('#appstore_bundle_inventorybundle_purchasevendoritem_quantity').val();
        var purchasePrice = $('#appstore_bundle_inventorybundle_purchasevendoritem_purchasePrice').val();
        purchasePrice = (purchasePrice != '') ? parseInt(purchasePrice) : 0;
        var salesPrice = $('#appstore_bundle_inventorybundle_purchasevendoritem_salesPrice').val();
        salesPrice = (salesPrice != '') ? parseInt(salesPrice) : 0;

        if (name == '') {
            $('#message').text('Item name field value is require');
            $('#appstore_bundle_inventorybundle_purchasevendoritem_name').focus();
            return false;
        } else if (!quantity.match(number_regex)) {
            $('#message').text('Item quantity field value is require');
            $('#appstore_bundle_inventorybundle_purchasevendoritem_quantity').focus();
            return false;
        } else if (!purchasePrice > 0) {
            $('#message').text('Item purchase price field value is require');
            $('#appstore_bundle_inventorybundle_purchasevendoritem_purchasePrice').focus();
            return false;
        } else if (!salesPrice > 0) {
            $('#message').text('Item sales price field value is require');
            $('#appstore_bundle_inventorybundle_purchasevendoritem_salesPrice').focus();
            return false;

        } else if (purchasePrice >= salesPrice) {
            $('#message').text('Item sales price must be more then purchase price');
            $('#appstore_bundle_inventorybundle_purchasevendoritem_salesPrice').focus();
            return false;

        } else {

            $.ajax({
                url: Routing.generate('inventory_purchasevendoritem_create', {purchase: purchase}),
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $('#dataProces').show().addClass('ajax-loading').fadeIn(3000);
                },
                success: function (response) {
                    $('#dataProces').fadeOut(3000);
                    obj = JSON.parse(response);
                    if (obj['success'] == 'success') {
                        $(':text').val("");
                        $('#purchaseVendorItem').html(obj['vendorItem']);
                        $('#message').html('Purchase vendor item added successfully');
                    } else if (obj['success'] == 'complete') {
                        window.location.href = purchaseItemUrl ;
                    } else {
                        $('#message').html('Purchase vendor item or quantity are not matching');
                        $(this).fadeOut(3000, function () {
                            location.reload();
                        })
                    }
                },

            })
        }
        e.preventDefault();

    });

    $('#purchaseVendorItem').on("click", ".removeVendorItem", function () {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function () {
                $('#dataProces').show().addClass('ajax-loading').fadeIn(3000);
            },
            success: function (response) {
                $('#dataProces').fadeOut(3000);
                if ('success' == response) {
                    $('#message').html('Data has been deleted successfully');
                    $('#remove-vendor-item-' + id).hide();
                }
            },
        })
    })

};
var inventoryItemImageGallery = function (item)
    {
        if (item > 0) {

            $("#pluploadUploader").pluploadQueue({

                // General settings
                runtimes: 'gears,browserplus,html5',
                url: Routing.generate('item_gallery', {item: item}),
                max_file_size: '10mb',
                chunk_size: '2mb',
                unique_names: true,
                sortable: true,
                // Resize images on clientside if we can
                resize: {width: 1024, height: 1024, quality: 90},
                // Specify what files to browse for
                filters: [
                    {title: "Image files", extensions: "jpeg,jpg,gif,png"},
                    {title: "Zip files", extensions: "zip"}
                ],

                // Flash settings
                flash_swf_url: 'theme/scripts/plupload/js/plupload.flash.swf',

                // Silverlight settings
                silverlight_xap_url: 'theme/scripts/plupload/js/plupload.silverlight.xap'

            });

        }
    }


