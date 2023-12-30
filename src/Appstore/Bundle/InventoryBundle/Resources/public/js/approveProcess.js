function ApproveProcess(){

    $( ".date-picker" ).datepicker({
        dateFormat: "dd-mm-yy"
    });

    $( ".dateCalendar" ).datepicker({
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
    });

    $(document).on("click", ".delete , .remove", function(event) {

        var url = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function() {
                $.get(url, function( data ) {
                    $(event.target).closest('tr').remove();
                });
            }
        });
    });


    $(document).on("click", ".approve", function() {
        var url = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get(url, function( data ) {
                    location.reload();
                });
            }
        });
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

    $(".selectAllItem").select2({

        placeholder: "Search item, color, size & brand name",
        ajax: {
            url: Routing.generate('item_search_all'),
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

    $(".branchSales2Item").select2({

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
        minimumInputLength:2
    });


    $(".stockBarcode").select2({

        placeholder: "Enter specific barcode",
        ajax: {

            url: Routing.generate('inventory_itembarcode_search'),
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
        formatSelection: function(item){return item.text}, // omitted for brevity, see the source of this page
        allowClear: true,
        minimumInputLength:2
    });

    $("#barcodeNo").select2({

        placeholder: "Enter specific barcode",
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
        minimumInputLength:2
    });

    $(".select2Barcode").select2({

        placeholder: "Enter specific barcode",
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
        formatSelection: function(item){return item.text}, // omitted for brevity, see the source of this page
        initSelection: function (element, callback) {
            var id = $(element).val();
            $.ajax(Routing.generate('inventory_barcode_name', { barcode : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength:2

    });


    $(".select2Grn").select2({

        placeholder: "Search purchase grn",
        ajax: {

            url: Routing.generate('inventory_grn_search'),
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
            $.ajax(Routing.generate('inventory_grn_name', { grn : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength:2
    });

    $(".select2Product").select2({

        placeholder: "Search product name",
        ajax: {

            url: Routing.generate('inventory_product_search'),
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
            $.ajax(Routing.generate('inventory_product_name', { product : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength:2
    });

    $(".select2Category").select2({

        placeholder: "Search by product category",
        ajax: {
            url: Routing.generate('inventory_category_search'),
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
            $.ajax(Routing.generate('inventory_category_name', { category : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength: 2
    });

    $(".select2Unit").select2({

        placeholder: "Search product unit",
        ajax: {
            url: Routing.generate('inventory_unit_search'),
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
            $.ajax(Routing.generate('inventory_unit_name', { unit : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength:1

    });

    $(".select2Color").select2({

        placeholder: "Search color name",
        ajax: {

            url: Routing.generate('inventory_itemcolor_search'),
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
            $.ajax(Routing.generate('inventory_itemcolor_name', { color : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });
        },
        allowClear: true,
        minimumInputLength:2
    });

    $(".select2Size").select2({

        placeholder: "Search size name",
        ajax: {

            url: Routing.generate('inventory_itemsize_search'),
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
            $.ajax(Routing.generate('inventory_itemsize_name', { size : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength:1
    });

    $(".select2Vendor").select2({

        placeholder: "Search vendor name",
        ajax: {
            url: Routing.generate('inventory_vendor_search'),
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
            $.ajax(Routing.generate('inventory_vendor_name', { vendor : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength:2

    });

    $(".select2Brand").select2({

        placeholder: "Search brand name",
        ajax: {

            url: Routing.generate('inventory_itembrand_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    q: params,
                    page_limit: 100,

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
            $.ajax(Routing.generate('inventory_itembrand_name', { brand : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });
        },
        allowClear: true,
        minimumInputLength: 2

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
            var id = $(element).val();
            $.ajax(Routing.generate('domain_user_name', { user : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });
        },
        allowClear: true,
        minimumInputLength:2
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

    $(".select2CustomerName").select2({

        ajax: {

            url: Routing.generate('domain_customer_auto_name_search'),
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

    $(".select2Location").select2({

        ajax: {

            url: Routing.generate('domain_location_search'),
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
            var location = $(element).val();
            $.ajax(Routing.generate('domain_location_name', { location : location}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });
        },
        allowClear: true,
        minimumInputLength: 2
    });


    $(document).on('change', '#item', function() {

        var item = $('#item').val();
        if(item == ''){
            $('#stockItemDetails').hide();
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_item_purchase',{'customer':'customer'}),
            type: 'POST',
            data:'item='+ item,
            success: function(response) {
                $('#stockItemDetails').show();
                $('#itemDetails').html(response);
                $(".editable").editable();
            },
        })
    });

    $("#sku").select2({

        placeholder: "Enter product sku",
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

    $(".select2Branch").select2({

        placeholder: "Search branch name",
        ajax: {
            url: Routing.generate('inventory_branches_search'),
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
            var branch = $(element).val();
            $.ajax(Routing.generate('inventory_branches_name', { branch : branch}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });
        },
        allowClear: true,
        minimumInputLength:2
    });




}

