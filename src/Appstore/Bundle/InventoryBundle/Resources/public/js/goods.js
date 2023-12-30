var InventoryItemListPage = function () {

    window.prepareBarCode = function () {
        var itemArr = $('input.barcode:checked').map(function () {
            return $(this).val();
        }).get();
        $.cookie('barcodes', itemArr, {path: '/'});
        return true;
    }


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
        minimumInputLength: 1
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
        minimumInputLength: 1
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
        minimumInputLength: 1
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
        minimumInputLength: 1

    });

    $(".select2Brand").select2({

        placeholder: "Search brand name",
        ajax: {

            url: Routing.generate('branding_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    q: params,
                    page_limit: 100 ,

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
            $.ajax(Routing.generate('branding_search_name', { brand : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength: 1

    });

    $(".select2Category").select2({

        placeholder: "Search category name",
        ajax: {

            url: Routing.generate('category_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    q: params,
                    page_limit: 100 ,

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
            $.ajax(Routing.generate('category_search_name', { brand : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength: 1

    });

}

var InventoryItemEditPage = function (item) {


    $('#addSubProduct').click(function() {

        var url = $(this).attr("data-url");
        var size = $('#goods_item_size').val();
        var unit = $('#goods_item_productUnit').val();
        var colors = $('#goods_item_colors').val();
        var salesPrice = $('#goods_item_purchasePrice').val();
        var purchasePrice = $('#goods_item_salesPrice').val();
        var quantity = $('#goods_item_quantity').val();
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data:'size='+size+"&colors="+colors+'&salesPrice='+salesPrice+'&purchasePrice='+purchasePrice+'&quantity='+quantity+'&unit='+unit,
                    success: function (response) {
                        $('#loadSubItem').html(response);
                        $('#goods_item_size, #goods_item_colors, #goods_item_productUnit').find('option').prop("selected", false);
                        $('#goods_item_purchasePrice,#goods_item_salesPrice,#goods_item_quantity').val('');
                    }
                })
            }
        });
    });

    $(document).on("click", ".removeAttr", function() {

        var url = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get(url, function( data ) {
                    $('.removeAttr-'+id).hide();
                });
            }
        });
    });

    $(document).on("click", ".removeProduct", function() {

        var url = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get(url, function( data ) {
                    $('#removeProduct-'+id).hide();
                });
            }
        });
    });


    $( "ol.singleSortable" ).on( "sortupdate", function( event, ui ) {

        serialized = $('ol.singleSortable').nestedSortable('serialize');
        $.ajax({
            url: Routing.generate('hotel_stock_attribute_sorted', {id: item}),
            type: "POST",
            data: serialized
        }).done(function(data){
            
        });
    });
    
    if (item > 0) {

        $("#pluploadUploader").pluploadQueue({

            // General settings
            runtimes: 'gears,browserplus,html5',
            url: Routing.generate('inventory_vendoritem_gallery', {item: item}),
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
            silverlight_xap_url: 'theme/scripts/plupload/js/plupload.silverlight.xap',
            init : {
                FilesAdded: function(up, files) {
                    var maxfiles = 6;
                    if(up.files.length > maxfiles )
                    {
                        up.splice(maxfiles);
                        alert('no more than '+maxfiles + ' file(s)');
                    }
                    if (up.files.length === maxfiles) {
                        $('#pluploadUploader').hide("slow"); // provided there is only one #uploader_browse on page
                    }
                }
            }

        });

    }

    $(document).on("click", ".remove, .delete", function() {
        var url = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get(url, function( data ) {
                    $('#remove-'+id).hide();
                });
            }
        });
    });


    $(document).on("click", ".addSubProduct", function() {

        var url = $(this).attr("data-url");
        $.ajax({
            url: url,
            type: 'POST',
            data:$('.action').serialize(),
            success: function (response) {
                $('#subProductLoad').html(response);
            }
        })

    });

    var count = 0;
    $('.addSubProductx').click(function(){

        var $el = $(this);
        var $cloneBlock = $('#clone-sub-product');
        var $clone = $cloneBlock.find('.clone:eq(0)').clone();
        $clone.find('[id]').each(function(){this.id+='someotherpart'});
        $clone.find(':text,textarea' ).val("");
        $clone.attr('id', "added"+(++count));
        $clone.find('.remove, .update').removeClass('hidden');
        $cloneBlock.append($clone);
        $('.numeric').numeric();

    });

    $('#clone-sub-product').on('click', '.remove', function(){
        $(this).closest('.clone').remove();
    });

    var count = 0;
    $('.addmore').click(function(){

        var $el = $(this);
        var $cloneBlock = $('#clone-block');
        var $clone = $cloneBlock.find('.clone:eq(0)').clone();
        $clone.find('[id]').each(function(){this.id+='someotherpart'});
        $clone.find(':text,textarea' ).val("");
        $clone.attr('id', "added"+(++count));
        $clone.find('.remove').removeClass('hidden');
        $cloneBlock.append($clone);
        $('.numeric').numeric();

    });

    $('#clone-block').on('click', '.remove', function(){
        $(this).closest('.clone').remove();
    });

}