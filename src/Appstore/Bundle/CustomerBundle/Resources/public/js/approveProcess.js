function ApproveProcess(){

    $( ".date-picker" ).datepicker({
        dateFormat: "yy-mm-dd"
    });
    // Getter
    var dateFormat = $( ".date-picker" ).datepicker( "option", "dateFormat" );

    // Setter
    $( ".date-picker" ).datepicker( "option", "dateFormat", "dd-mm-yy" );

    $(document).on("click", "#submitProcess", function(e) {

        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $('form#orderProcess').submit();
            }
        });
        e.preventDefault();
    });
    $(document).on("change", "#ecommerce_order_cashOnDelivery", function(e) {
        var val = this.checked ? 'hide' : 'show';
        if(val === "show"){
            $('#transactionInfo').removeClass('hide').addClass('show');
        }else{
            $('#transactionInfo').removeClass('show').addClass('hide');
        }
    });

    $('#check').on('change', function() {
        var val = this.checked ? this.value : '';
        $('#show').html(val);
    });

    $(document).on("click", " .approve, .confirm, .remove , .process , .remove-tr , .item-disable , .item-remove", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
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


    $(document).on("click", ".isApplicable", function() {
        var url = $(this).attr('data-url');
        $.get(url);
    });

    $('.btn-number').click(function(e){

        e.preventDefault();

        url = $(this).attr('data-url');
        var productId = $(this).attr('data-text');
        var price = $(this).attr('data-title');
        fieldId = $(this).attr('data-id');
        fieldName = $(this).attr('data-field');
        type      = $(this).attr('data-type');
        var size = $('#size-'+fieldId).val() != '' ? $('#size-'+fieldId).val() : '';
        var color = $('#color-'+fieldId).val() != '' ? $('#color-'+fieldId).val() : '';
        var input = $('#quantity-'+$(this).attr('data-id'));
        var currentVal = parseInt(input.val());
        if (!isNaN(currentVal)) {
            if(type == 'minus') {
                if(currentVal > input.attr('min')) {
                    var existVal = (currentVal - 1);
                    input.val(existVal).change();
                    $.get( url,{ quantity:existVal,'productId':productId,'price':price,'size':size,'color':color})
                        .done(function( data ) {
                            location.reload();
                        });
                }
                if(parseInt(input.val()) == input.attr('min')) {
                    $(this).attr('disabled', true);
                }

            } else if(type == 'plus') {

                if(currentVal < input.attr('max')) {
                    var existVal = (currentVal + 1);
                    input.val(existVal).change();
                    $.get( url,{ quantity:existVal,'productId':productId,'price':price,'size':size,'color':color})
                        .done(function(data){
                            if(data == 'success'){
                                location.reload();
                            }else{
                                input.val(existVal-1).change();
                                alert('There is not enough product in stock at this moment')
                            }
                        });
                }
                if(parseInt(input.val()) == input.attr('max')) {
                    $(this).attr('disabled', true);
                }

            }
        } else {
            input.val(0);
        }
    });

    $('.itemProcess').click(function(e){
        
        var rel = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        var quantity = $('#quantity-'+rel).val() != '' ? $('#quantity-'+rel).val() : 1;
        var convertRate = $('#convertRate-'+rel).val() != '' ? parseFloat($('#convertRate-'+rel).val()) : 0;
        var shippingCharge = $('#shippingCharge-'+rel).val() != '' ? $('#shippingCharge-'+rel).val() : 0;
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get( url,{quantity:quantity,convertRate:convertRate,shippingCharge:shippingCharge})
                    .done(function(data){
                        location.reload();
                    });
            }
        });
        e.preventDefault();
    });

    $('.orderItemProcess').click(function(e){
        
        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        var quantity = $('#quantity-'+id).val() != '' ? $('#quantity-'+id).val() :1;
        var size = $('#size-'+id).val() != '' ? $('#size-'+id).val() : '';
        var color = $('#color-'+id).val() != '' ? $('#color-'+id).val() : '';

        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get( url,'quantity='+quantity+'&size='+size+'&color='+color)
                    .done(function(data){
                        location.reload();
                    });
            }
        });
        e.preventDefault();
    });

    $('#customerSubmitPayment').click( function( e ) {

        var url = $('#ecommerce-payment').attr("action");
        var amount = $('#ecommerce_payment_amount').val();
        if( amount == "" ){
            alert( "Please add payment amount" );
            $('#ecommerce_payment_amount').focus();
            return false;
        }
        var accountMobileBank = $('#ecommerce_payment_accountMobileBank').val();
        if( accountMobileBank == "" ){
            alert( "Please payment mobile account" );
            $('#ecommerce_payment_accountMobileBank').focus();
            return false;
        }
        var mobileAccount = $('#ecommerce_payment_mobileAccount').val();
        if( mobileAccount == "" ){
            alert( "Please add payment mobile no" );
            $('#ecommerce_payment_mobileAccount').focus();
            return false;
        }
        var transaction = $('#ecommerce_payment_transaction').val();
        if( transaction == "" ){
            alert( "Please add payment transaction no" );
            $('#ecommerce_payment_transaction').focus();
            return false;
        }
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.post( url,{amount:amount,'accountMobileBank':accountMobileBank,'mobileAccount':mobileAccount,'transaction':transaction})
                    .done(function(data){
                        $('#payment-confirm').notifyModal({
                            duration : 4000,
                            placement : 'center',
                            overlay : true,
                            type : 'notify',
                            icon : false
                        });
                        setTimeout(function(){
                            location.reload();
                        }, 5000);

                });
            }
        });
        e.preventDefault();

    });

    $('#adminSubmitPayment').click( function( e ) {

        var url = $('#ecommerce-payment').attr("action");

        var amount = $('#ecommerce_payment_amount').val();
        if( amount == "" ){
            alert( "Please add payment amount" );
            $('#ecommerce_payment_amount').focus();
            return false;
        }
        var transactionType = $('#ecommerce_payment_transactionType').val();
        if( transactionType == "" ){
            alert( "Please select transaction type" );
            $('#ecommerce_payment_transactionType').focus();
            return false;
        }

        var accountMobileBank = $('#ecommerce_payment_accountMobileBank').val();
        if( accountMobileBank == "" ){
            alert( "Please payment mobile account" );
            $('#ecommerce_payment_accountMobileBank').focus();
            return false;
        }

        var mobileAccount = $('#ecommerce_payment_mobileAccount').val();
        if( mobileAccount == "" ){
            alert( "Please add payment mobile no" );
            $('#ecommerce_payment_mobileAccount').focus();
            return false;
        }
        var transaction = $('#ecommerce_payment_transaction').val();
        if( transaction == "" ){
            alert( "Please add payment transaction no" );
            $('#ecommerce_payment_transaction').focus();
            return false;
        }

        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.post( url,{amount:amount,transactionType:transactionType,'accountMobileBank':accountMobileBank,'mobileAccount':mobileAccount,'transaction':transaction})
                    .done(function(data){
                        location.reload();
                    });
            }
        });
        e.preventDefault();

    });

    $(document).on('click', '.view', function() {

        var url = $(this).attr("data-url");
        var title = $(this).attr("data-title");
        $('.dialogModal_header').html(title);
        $('.dialog_content').dialogModal({
            topOffset: 0,
            top: 0,
            type: '',
            onOkBut: function(event, el, current) {},
            onCancelBut: function(event, el, current) {},
            onLoad: function(el, current) {
                $.ajax({
                    url:url,
                    async: true,
                    success: function (response) {
                        el.find('.dialogModal_content').html(response);
                    }
                });
            },
            onClose: function(el, current) {},
            onChange: function(el, current) {}
        });

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
        minimumInputLength: 1
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


    /*$('#orderProcess').submit( function( e ) {

        var url = $('#orderProcess').attr("action");

        var comment = $('#ecommerce_order_comment').val();
        var address = $('#ecommerce_order_address').val();
        var location = $('#ecommerce_order_location').val();
        var process = $('#ecommerce_order_process').val();
        var deliveryDate = $('#ecommerce_order_deliveryDate').val();
        var cashOnDelivery = $('#ecommerce_order_cashOnDelivery').val();

        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.post( url,{comment:comment,process:process,deliveryDate:deliveryDate,address:address,location:location,cashOnDelivery:cashOnDelivery})
                    .done(function(data){
                        location.reload();
                });
            }
        });
        e.preventDefault();

    });*/

}

