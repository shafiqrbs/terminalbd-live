var InventoryItemListPage = function () {

    var items = $.cookie('itemBarcode') ? $.cookie('itemBarcode').split(',') : [];
    $('body').on('change', 'input.itemBarcode', function(el) {
        console.log(items);
        var val = $(this).val();
        if($(this).prop('checked')) {
            items.push(val);
        }else{
            var index = items.indexOf(val);
            if (index > -1) {
                items.splice(index, 1);
            }
        }
        $.cookie('itemBarcode', items, {path: '/'});
    });


    var itemArr = $.cookie('barcodes') ? $.cookie('barcodes').split(',') : [];

    $('body').on('change', 'input.barcode', function(el) {
        console.log(itemArr);
        var val = $(this).val();
        if($(this).prop('checked')) {
            itemArr.push(val);
        }else{
            var index = itemArr.indexOf(val);
            if (index > -1) {
                itemArr.splice(index, 1);
            }
        }
        $.cookie('barcodes', itemArr, {path: '/'});
    });


    $(document).on("click", ".skuUpdate", function() {
        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $.get(url, function(response){
            $('#sku-'+id).html(response);
        });
    });
};

var InventoryItemEditPage = function (item) {



    /**
     * Created by rbs on 2/12/16.
     */
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
                    var maxfiles = 2;
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

    $(document).on("click", ".barcode", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response ) {
                    location.reload();
                }
            },
        })

    });



    var count = 0;

    $('.addmore').click(function(){

        var $el = $(this);
        alert($el);
        var $cloneBlock = $('#clone-block-1');
        var $clone = $cloneBlock.find('.clone:eq(0)').clone();
        $clone.find('[id]').each(function(){this.id+='someotherpart'});
        $clone.find(':text,textarea' ).val("");
        $clone.attr('id', "added"+(++count));
        $clone.find('.remove').removeClass('hidden');
        $cloneBlock.append($clone);
        $('.numeric').numeric();
    });

    $('form.addPurchase').on('click', '.remove', function(){
        $(this).closest('.clone').remove();
    });

}