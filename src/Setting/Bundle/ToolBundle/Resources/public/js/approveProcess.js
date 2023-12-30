function ApproveProcessInt(){


    $('#bankbox').hide();
    $('#bkashbox').hide();
    var paymentMethod = $('#paymentMethod').val();

    if(paymentMethod == 'Bank'){
        $('#bankbox').show();
        $('#bkashbox').hide();
    }
    if(paymentMethod == 'bKash'){
        $('#bankbox').hide();
        $('#bkashbox').show();
    }

    $( ".date-picker" ).datepicker({
        dateFormat: "yy-mm-dd"
    });
    // Getter
    var dateFormat = $( ".date-picker" ).datepicker( "option", "dateFormat" );

    // Setter
    $( ".date-picker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );

    $('#payment').submit( function( e ) {

        var url = $('#submitted').attr("data-url");
        var paymentMethod = $('#paymentMethod').val();
        var bank = $('#bank').val();
        var bkash = $('#bkash').val();
        if( paymentMethod == "" ){
            alert( "Please select payment method!" );
            return false;
        }
        if( paymentMethod == 'Bank' && bank == ''){
            alert( "Please select payment bank account no" );
            return false;
        }
        if( paymentMethod == 'bKash' && bkash == ''){
            alert( "Please select payment bkash account" );
            return false;
        }
        $.ajax({
            url:url,
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.ajax-loading').show().addClass('loading').fadeIn(3000);
            },
            success: function(response) {
                location.reload();
            }
        });

        e.preventDefault();

    });

    $(document).on("click", ".delete", function() {
        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                if ('success' == response ) {
                    $('#remove-' + id).remove();
                }
                location.reload();
            }
        });
    });


    $(document).on("click", ".reset", function() {
        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get(url, function( data ) {
                    alert("This domain data reset successfully")
                });
            }
        });
    });


    $(document).on("click", ".approve", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
               location.reload();
            },
        })

    })

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

    $('form.addPurchase').on('click', '.remove', function(){
        $(this).closest('.clone').remove();
    });

}

