/**
 * Created by rbs on 5/1/17.
 */

function jqueryInstantLoad(){


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

                case 'quantity':
                    $('#addInstantPurchase').focus();
                    break;
                case 'addInstantPurchase':
                    $('#vendor').select2('open');
                    break;
            }
            return false;
        }
    });

    $(document).on('click', '#addInstantPurchase', function() {

        var form = $("#instantPurchase").validate({

            rules: {

                "medicineName": {required: true},
                "vendor": {required: true},
                "purchasesBy": {required: true},
                "purchasePrice": {required: true},
                "expirationStartDate": {required: false},
                "expirationEndDate": {required: false},
                "quantity": {required: true}
            },

            messages: {

                "medicineName": "Enter medicine name",
                "vendor": "Select vendor name",
                "purchasesBy": "Enter purchase by medicine",
                "purchasePrice": "Enter purchase price",
                "quantity": "Enter medicine quantity"
            },
            tooltip_options: {
                "medicineName": {placement: 'top', html: true},
                "vendor": {placement: 'top', html: true},
                "purchasesBy": {placement: 'top', html: true},
                "purchasePrice": {placement: 'top', html: true},
                "quantity": {placement: 'top', html: true}
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
                        $('#instantPurchaseItem').html(obj['instantPurchaseItem']);
                        $('#instantPurchase')[0].reset();
                        $("#medicineName").select2("val", "");
                        $("#purchasesBy").select2("val", "");
                    }
                });
            }
        });
    });

    $(document).on('click', '.instantSales', function() {

        var url = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        var quantity = parseInt($('#quantity-'+id).val());
        var remainingQnt = parseInt($('#remainingQnt-'+id).val());
        if(isNaN(quantity) === true){
            alert('Please add medicine/accessories quantity');
            $('#quantity-'+id).focus();
            return false;
        }
        if(quantity >= remainingQnt ){
            alert('Medicine/accessories quantity is not enough.');
            $('#quantity-'+id).focus();
            return false;
        }
        $.ajax({
            url:url,
            type: 'POST',
            data:'quantity='+ quantity,
            success: function(response){
                obj = JSON.parse(response);
                $('#invoiceParticulars').html(obj['salesItems']);
                $('#subTotal').html(obj['subTotal']);
                $('#vat').val(obj['vat']);
                $('.grandTotal').html(obj['netTotal']);
                $('#paymentTotal').val(obj['netTotal']);
                $('#due').val(obj['due']);
                $('.dueAmount').html(obj['due']);
                $('#quantity-'+id).val('');
            }
        })

    });

    $(document).on("click", ".instantDelete", function() {

        var url = $(this).attr("data-url");
        var id = $(this).attr("id");
        $.get(url, function(data, status){
            $('#removeInstantItem-'+id).hide();
        });

    });

    $(".select2StockMedicine").select2({

        placeholder: "Search vendor name",
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
        formatResult: function (item) { return item.text}, // omitted for brevity, see the source of this page
        formatSelection: function (item) { return item.text }, // omitted for brevity, see the source of this page
        initSelection: function (element, callback) {
            var id = $(element).val();
            $.ajax(Routing.generate('medicine_stock_name', { vendor : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });
        },
        allowClear: true,
        minimumInputLength: 1

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




