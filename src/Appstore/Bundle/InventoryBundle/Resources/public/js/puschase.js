function InventoryPurchasePage(){

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
    $('#addPurchaseForm').click(function(e) {

        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $('#purchaseItemForm').submit();
                EditableInit();
            }
        });
        e.preventDefault();
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

    $('#addStockItem').click(function(e) {

        var url =  $('#inventoryItem').attr("action");

        var masterItem = $('#masterItem').val();
        if( masterItem == "" ){
            alert( "Please add  item name" );
            $('#masterItem').focus();
            return false;
        }
        var color = $('#color').val();
        if( color == "" ){
            alert( "Please add color name" );
            $('#color').focus();
            return false;
        }
        var size = $('#size').val();
        if( size == "" ){
            alert( "Please add size value" );
            $('#size').focus();
            return false;
        }

        var brand = $('#brand').val();
        if( brand == "" ){
            alert( "Please add brand name" );
            $('#brand').focus();
            return false;
        }

        var vendor = $('#vendor').val();
        if( vendor == "" ){
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

    $('#appstore_bundle_inventorybundle_purchase_memo').attr("required", true);
    $('#appstore_bundle_inventorybundle_purchase_dueAmount').attr("disabled", true);
    $('#appstore_bundle_inventorybundle_purchase_totalAmount , #appstore_bundle_inventorybundle_purchase_paymentAmount ,#appstore_bundle_inventorybundle_purchase_commissionAmount ').change(function() {

        var totalAmount = ($('#appstore_bundle_inventorybundle_purchase_totalAmount').val());
        total = (totalAmount != '') ? parseInt(totalAmount) : 0;
        var paymentAmount = $('#appstore_bundle_inventorybundle_purchase_paymentAmount').val();
        payment = ( paymentAmount != '') ? parseInt(paymentAmount) : 0;
        var commissionPayment = ($('#appstore_bundle_inventorybundle_purchase_commissionAmount').val());
        commission = (commissionPayment != '') ? parseInt(commissionPayment) : 0;
        //var due = (total -  ( payment + commission));
        var due = (total - payment);
        $('#appstore_bundle_inventorybundle_purchase_dueAmount').val(due);
        if (paymentAmount = ""){
            alert(total);
            $('#appstore_bundle_inventorybundle_purchase_paymentAmount').val(total);
        }

    })

    $('.clone-block').on("change", ".quantity , .purchasePrice ", function() {

        var purchasePrice = $("input[name='purchasePrice[]']").val();
        purchasePrice = (purchasePrice != '') ?  parseInt(purchasePrice) : 0 ;
        var quantity = $("input[name='quantity[]']").val();
        quantity = (quantity != '') ?  parseInt(quantity) : 0 ;
        var subTotal = ( purchasePrice * quantity );
        $(this).closest('.subTotalPurchase').val(subTotal);

    });

    var validator =  $("from#purchaseForm").validate({

        rules: {

            "appstore_bundle_inventorybundle_purchase[vendor]": {required: true},
            "appstore_bundle_inventorybundle_purchase[memo]": {required: true},
            "appstore_bundle_inventorybundle_purchase[totalItem]": {required: true},
            "appstore_bundle_inventorybundle_purchase[totalQnt]": {required: true},
            "appstore_bundle_inventorybundle_purchase[totalAmount]": {required: true},
            "appstore_bundle_inventorybundle_purchase[paymentAmount]": {required: true}
        },

        messages: {

            "appstore_bundle_inventorybundle_purchase[vendor]":"Enter your vendor name",
            "appstore_bundle_inventorybundle_purchase[memo]":"Enter your vendor name",
            "appstore_bundle_inventorybundle_purchase[totalItem]":"Enter your vendor name",
            "appstore_bundle_inventorybundle_purchase[totalQnt]":"Enter your vendor name",
            "appstore_bundle_inventorybundle_purchase[totalAmount]":"Enter your vendor name",
            "appstore_bundle_inventorybundle_purchase[paymentAmount]":"Enter your vendor name"
        },

        tooltip_options: {

            "appstore_bundle_inventorybundle_purchase[vendor]": {placement:'top',html:true},
            "appstore_bundle_inventorybundle_purchase[memo]": {placement:'top',html:true},
            "appstore_bundle_inventorybundle_purchase[totalItem]": {placement:'top',html:true},
            "appstore_bundle_inventorybundle_purchase[totalQnt]": {placement:'top',html:true},
            "appstore_bundle_inventorybundle_purchase[totalAmount]": {placement:'top',html:true},
            "appstore_bundle_inventorybundle_purchase[paymentAmount]": {placement:'top',html:true}

        },
        submitHandler: function() {

            $(this).submit();

        }

    });

    var count = 0;
    $('.addmore').click(function(){
        var $el = $(this);
        $vendor_id = $el.data('ref-id');
        var $cloneBlock = $('#clone-block-'+ $vendor_id);

        var $clone = $cloneBlock.find('.clone:eq(0)').clone();
        $clone.find('[id]').each(function(){this.id+='someotherpart'});
        $clone.find(':text,textarea' ).val("");
        $clone.attr('id', "added"+(++count));
        $clone.find('.remove').removeClass('hidden');
        $cloneBlock.append($clone);
        $('.numeric').numeric();
    });

    $('form.purchase').on('click', '.remove', function(){
        $(this).closest('.clone').remove();
    });

    $(document).on('change', '.mode', function() {
        var url = $(this).val();
        if(url === ''){
            alert('You have to add particulars from drop down and this not service item');
            return false;
        }
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                console.log(response);
            }
        })
    });
}

